<?php

use App\Actions\ApproveMaterial;
use App\Actions\CancelMaterial;
use App\Actions\ChangePassword;
use App\Actions\CreateMaterial;
use App\Actions\DeleteMaterial;
use App\Actions\RegisterUser;
use App\Actions\RejectMaterial;
use App\Actions\ReviseMaterial;
use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Models\AuditEvent;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

function auditMaterial(array $attributes = []): Material
{
    return Material::factory()->create(['status' => MaterialStatus::Pendente->value, ...$attributes]);
}

function onlyEvent(AuditAction $action): AuditEvent
{
    expect(AuditEvent::count())->toBe(1);

    return tap(AuditEvent::sole(), fn (AuditEvent $event) => expect($event->action)->toBe($action));
}

it('registra o cadastro e a troca de senha', function () {
    $user = app(RegisterUser::class)->handle('Ana Lima', 'ana@academico.ifsul.edu.br', 'senha-segura', '0.2');
    $event = onlyEvent(AuditAction::UserRegistered);
    expect([$event->actor_id, $event->user_id, $event->data])->toEqual([$user->id, $user->id, []]);

    app(ChangePassword::class)->handle($user, 'outra-senha-1', 'sessao');
    $event = AuditEvent::where('action', AuditAction::PasswordChanged)->sole();
    expect([$event->actor_id, $event->user_id, $event->data])->toEqual([$user->id, $user->id, []]);
});

it('não registra cadastro com e-mail repetido', function () {
    User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    expect(fn () => app(RegisterUser::class)->handle('Ana', 'ana@academico.ifsul.edu.br', 'senha-segura', '0.2'))
        ->toThrow(ValidationException::class);
    expect(AuditEvent::count())->toBe(0);
});

it('registra o envio anônimo com o autor real', function () {
    $author = User::factory()->create();
    $material = app(CreateMaterial::class)->handle($author, Discipline::first(), [
        'title' => 'Resumo', 'description' => null, 'url' => 'https://example.com/resumo', 'anonymous' => true,
    ]);

    $event = onlyEvent(AuditAction::MaterialSubmitted);
    expect([$event->actor_id, $event->material_id, $event->user_id])->toBe([$author->id, $material->id, null])
        ->and($event->data)->toEqual(['anonymous' => true, 'revision' => false]);
});

it('não registra envio com link repetido', function () {
    $taken = Material::factory()->approved()->create();

    expect(fn () => app(CreateMaterial::class)->handle(User::factory()->create(), $taken->discipline, [
        'title' => 'Outro', 'description' => null, 'url' => $taken->url, 'anonymous' => false,
    ]))->toThrow(ValidationException::class);
    expect(AuditEvent::count())->toBe(0);
});

it('registra o envio de alteração', function () {
    $original = Material::factory()->approved()->create();

    $revision = app(ReviseMaterial::class)->handle($original, ['title' => 'Novo', 'description' => null, 'url' => 'https://example.com/novo', 'anonymous' => false]);

    $event = onlyEvent(AuditAction::RevisionSubmitted);
    expect([$event->actor_id, $event->material_id])->toBe([$original->user_id, $revision->id])
        ->and($event->data)->toEqual(['anonymous' => false, 'revision' => true]);
});

it('registra o cancelamento só quando o envio ainda estava pendente', function () {
    $material = auditMaterial();

    expect(app(CancelMaterial::class)->handle($material))->toBeTrue();
    $event = onlyEvent(AuditAction::SubmissionCancelled);
    expect([$event->actor_id, $event->material_id])->toBe([$material->user_id, $material->id]);

    expect(app(CancelMaterial::class)->handle($material))->toBeFalse();
    expect(AuditEvent::count())->toBe(1);
});

it('registra a exclusão, e não registra quando ela é recusada', function () {
    $material = Material::factory()->approved()->create(['anonymous' => true]);

    app(DeleteMaterial::class)->handle($material);
    $event = onlyEvent(AuditAction::MaterialDeleted);
    expect([$event->actor_id, $event->material_id])->toBe([$material->user_id, $material->id])
        ->and($event->data)->toEqual(['anonymous' => true, 'revision' => false]);

    expect(fn () => app(DeleteMaterial::class)->handle(auditMaterial()))->toThrow(HttpResponseException::class);
    expect(AuditEvent::count())->toBe(1);
});

it('registra a aprovação e a rejeição com quem decidiu', function () {
    $moderator = User::factory()->create(['role' => Role::Moderador]);
    $approved = auditMaterial();
    $rejected = auditMaterial();

    app(ApproveMaterial::class)->handle($approved, $moderator);
    app(RejectMaterial::class)->handle($rejected, $moderator, 'Fora do tema.');

    $approval = AuditEvent::where('action', AuditAction::MaterialApproved)->sole();
    $rejection = AuditEvent::where('action', AuditAction::MaterialRejected)->sole();
    expect([$approval->actor_id, $approval->material_id])->toBe([$moderator->id, $approved->id])
        ->and([$rejection->actor_id, $rejection->material_id])->toBe([$moderator->id, $rejected->id])
        ->and($rejection->data)->toEqual(['anonymous' => false, 'revision' => false]);
});

it('não registra decisão que chega tarde', function () {
    $moderator = User::factory()->create(['role' => Role::Moderador]);
    $material = auditMaterial();
    app(CancelMaterial::class)->handle($material);

    expect(app(ApproveMaterial::class)->handle($material, $moderator))->toBeFalse()
        ->and(app(RejectMaterial::class)->handle($material, $moderator, 'Atrasado.'))->toBeFalse();
    expect(AuditEvent::where('action', '!=', AuditAction::SubmissionCancelled)->count())->toBe(0);
});

it('guarda na aprovação da alteração a versão publicada antes', function () {
    $original = Material::factory()->approved()->create(['title' => 'Antigo', 'description' => 'Texto antigo', 'url' => 'https://example.com/antigo']);
    $revision = app(ReviseMaterial::class)->handle($original, ['title' => 'Novo', 'description' => null, 'url' => 'https://example.com/novo', 'anonymous' => false]);

    app(ApproveMaterial::class)->handle($revision, User::factory()->create(['role' => Role::Moderador]));

    $event = AuditEvent::where('action', AuditAction::MaterialApproved)->sole();
    expect($event->material_id)->toBe($revision->id)
        ->and($event->data)->toEqual([
            'anonymous' => false,
            'revision' => true,
            'previous' => ['title' => 'Antigo', 'description' => 'Texto antigo', 'url' => 'https://example.com/antigo'],
        ])
        ->and($original->fresh()->title)->toBe('Novo');
});

it('nenhum evento guarda e-mail, senha, hash, token ou IP', function () {
    $user = app(RegisterUser::class)->handle('Ana Lima', 'ana@academico.ifsul.edu.br', 'senha-segura', '0.2');
    app(ChangePassword::class)->handle($user, 'outra-senha-1', 'sessao');
    $original = app(CreateMaterial::class)->handle($user, Discipline::first(), ['title' => 'A', 'description' => null, 'url' => 'https://example.com/a', 'anonymous' => true]);
    app(ApproveMaterial::class)->handle($original, User::factory()->create(['role' => Role::Moderador]));
    $revision = app(ReviseMaterial::class)->handle($original->fresh(), ['title' => 'B', 'description' => null, 'url' => 'https://example.com/b', 'anonymous' => true]);
    app(RejectMaterial::class)->handle($revision, User::factory()->create(['role' => Role::Moderador]), 'Motivo sigiloso.');

    $stored = json_encode(AuditEvent::pluck('data'));
    $hash = $user->fresh()->password;
    expect($stored)->not->toContain('ana@', 'senha-segura', 'outra-senha-1', $hash, 'Motivo sigiloso', '127.0.0.1');
    expect(collect(AuditEvent::pluck('data'))->flatMap(fn (array $data) => array_keys($data))->unique()->sort()->values()->all())
        ->toBe(['anonymous', 'revision']);
});
