<?php

use App\Actions\ChangeUserRole;
use App\Actions\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Models\AuditEvent;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

function auditEvent(): AuditEvent
{
    $actor = User::factory()->create();
    app(RecordAuditEvent::class)->handle(AuditAction::PasswordChanged, $actor, $actor);

    return AuditEvent::sole();
}

it('o banco recusa alterar, apagar ou esvaziar eventos', function (Closure $write) {
    $event = auditEvent();

    expect(fn () => DB::transaction(fn () => $write($event)))->toThrow(QueryException::class);
    expect(AuditEvent::count())->toBe(1);
})->with([
    'update' => [fn (AuditEvent $event) => DB::table('audit_events')->where('id', $event->id)->update(['action' => 'role_changed'])],
    'delete' => [fn (AuditEvent $event) => DB::table('audit_events')->where('id', $event->id)->delete()],
    'truncate' => [fn () => DB::statement('TRUNCATE audit_events')],
]);

it('o banco recusa ação desconhecida e evento sem autor fora da troca de papel', function (array $row) {
    $user = User::factory()->create();

    expect(fn () => DB::table('audit_events')->insert([...$row, 'user_id' => $user->id, 'data' => '{}', 'created_at' => now()]))
        ->toThrow(QueryException::class);
})->with([
    'ação desconhecida' => [['action' => 'user_viewed']],
    'sem autor' => [['action' => 'password_changed', 'actor_id' => null]],
]);

it('registra a troca de papel pelo painel', function () {
    $admin = User::factory()->create(['role' => Role::Administrador]);
    $target = User::factory()->create();

    app(ChangeUserRole::class)->handle($admin, $target, Role::Moderador);

    $event = AuditEvent::sole();
    expect($event->action)->toBe(AuditAction::RoleChanged)
        ->and($event->actor_id)->toBe($admin->id)
        ->and($event->user_id)->toBe($target->id)
        ->and($event->material_id)->toBeNull()
        ->and($event->data)->toEqual(['from' => 'ALUNO', 'to' => 'MODERADOR', 'via' => 'painel']);
});

it('não registra quando o papel é o mesmo ou a troca é recusada', function () {
    $admin = User::factory()->create(['role' => Role::Administrador]);
    $target = User::factory()->create(['role' => Role::Moderador]);

    app(ChangeUserRole::class)->handle($admin, $target, Role::Moderador);
    expect(fn () => app(ChangeUserRole::class)->handle($admin, $admin, Role::Aluno))->toThrow(Exception::class);

    expect(AuditEvent::count())->toBe(0);
});

it('registra a troca de papel pelo workflow sem autor', function () {
    $user = User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    $this->artisan('conectacc:promote', ['email' => 'ana@academico.ifsul.edu.br', 'role' => 'MODERADOR'])->assertSuccessful();

    $event = AuditEvent::sole();
    expect($event->actor_id)->toBeNull()
        ->and($event->user_id)->toBe($user->id)
        ->and($event->data)->toEqual(['from' => 'ALUNO', 'to' => 'MODERADOR', 'via' => 'workflow']);
});

it('não registra pelo workflow quando o papel é o mesmo', function () {
    User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    $this->artisan('conectacc:promote', ['email' => 'ana@academico.ifsul.edu.br', 'role' => 'ALUNO'])->assertSuccessful();

    expect(AuditEvent::count())->toBe(0);
});

it('junta os dados extras aos da oportunidade', function () {
    $opportunity = Opportunity::factory()->create(['title' => 'Vaga', 'type' => 'ESTAGIO']);

    app(RecordAuditEvent::class)->handle(AuditAction::OpportunityRejected, $opportunity->author, data: ['note' => 'x'], opportunity: $opportunity);

    expect(AuditEvent::sole()->data)->toEqual(['title' => 'Vaga', 'type' => 'ESTAGIO', 'note' => 'x']);
});
