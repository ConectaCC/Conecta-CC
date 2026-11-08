<?php

use App\Actions\ApproveMaterial;
use App\Actions\ChangeUserRole;
use App\Actions\CreateMaterial;
use App\Actions\RecordAuditEvent;
use App\Actions\ReviseMaterial;
use App\Enums\AuditAction;
use App\Enums\Role;
use App\Filament\Resources\AuditEvents\Pages\ListAuditEvents;
use App\Models\AuditEvent;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->create(['role' => Role::Administrador, 'display_name' => 'Alice Admin']);
    $this->actingAs($this->admin, 'web');
});

it('fica fechada para quem não é ADMINISTRADOR', function (Role $role) {
    $this->actingAs(User::factory()->create(['role' => $role]), 'web')->get('/admin/auditoria')->assertForbidden();
})->with([Role::Aluno, Role::Moderador]);

it('abre a página para o ADMINISTRADOR', function () {
    $this->get('/admin/auditoria')->assertOk()->assertSee('Auditoria')->assertSee('Consulte o que foi feito no sistema e por quem.');
});

it('mostra a troca de papel com o resumo e o workflow', function () {
    $target = User::factory()->create(['display_name' => 'Bruno Alvo']);
    app(ChangeUserRole::class)->handle($this->admin, $target, Role::Moderador);
    app(RecordAuditEvent::class)->handle(AuditAction::RoleChanged, null, $target, data: ['from' => 'MODERADOR', 'to' => 'ALUNO', 'via' => 'workflow']);
    [$panel, $workflow] = AuditEvent::orderBy('id')->get();

    Livewire::test(ListAuditEvents::class)
        ->assertCanSeeTableRecords([$panel, $workflow])
        ->assertTableColumnStateSet('action', 'Papel alterado', $panel)
        ->assertTableColumnStateSet('actor', 'Alice Admin', $panel)
        ->assertTableColumnStateSet('actor', 'Workflow do GitHub', $workflow)
        ->assertTableColumnStateSet('subject', 'Bruno Alvo', $panel)
        ->assertTableColumnStateSet('summary', 'Aluno → Moderador', $panel)
        ->assertTableColumnStateSet('summary', 'Moderador → Aluno', $workflow);
});

it('mostra quem enviou o material anônimo, e a marca só abaixo do autor', function () {
    $author = User::factory()->create(['display_name' => 'Autora Sigilosa']);
    $moderator = User::factory()->create(['role' => Role::Moderador, 'display_name' => 'Marcos Moderador']);
    $material = app(CreateMaterial::class)->handle($author, Discipline::first(), ['title' => 'Resumo anônimo', 'description' => null, 'url' => 'https://example.com/anon', 'anonymous' => true]);
    app(ApproveMaterial::class)->handle($material, $moderator);

    $page = Livewire::test(ListAuditEvents::class)
        ->assertSee('Autora Sigilosa')
        ->assertSee($material->code.' · Resumo anônimo');
    $submitted = AuditEvent::where('action', AuditAction::MaterialSubmitted)->sole();
    $approved = AuditEvent::where('action', AuditAction::MaterialApproved)->sole();
    $page->assertTableColumnStateSet('actor', 'Autora Sigilosa', $submitted)
        ->assertTableColumnStateSet('actor', 'Marcos Moderador', $approved);

    expect(substr_count($page->html(), 'Enviado anonimamente'))->toBe(1);
});

it('filtra por ação, por quem fez e por período', function () {
    $other = User::factory()->create();
    app(ChangeUserRole::class)->handle($this->admin, $other, Role::Moderador);
    $this->travel(3)->days();
    app(RecordAuditEvent::class)->handle(AuditAction::PasswordChanged, $other, $other);
    [$roleEvent, $passwordEvent] = AuditEvent::orderBy('id')->get();

    Livewire::test(ListAuditEvents::class)
        ->filterTable('action', 'password_changed')
        ->assertCanSeeTableRecords([$passwordEvent])->assertCanNotSeeTableRecords([$roleEvent]);

    Livewire::test(ListAuditEvents::class)
        ->filterTable('actor_id', $this->admin->id)
        ->assertCanSeeTableRecords([$roleEvent])->assertCanNotSeeTableRecords([$passwordEvent]);

    Livewire::test(ListAuditEvents::class)
        ->filterTable('period', ['from' => now()->toDateString(), 'until' => null])
        ->assertCanSeeTableRecords([$passwordEvent])->assertCanNotSeeTableRecords([$roleEvent]);

    Livewire::test(ListAuditEvents::class)
        ->filterTable('period', ['from' => null, 'until' => now()->subDay()->toDateString()])
        ->assertCanSeeTableRecords([$roleEvent])->assertCanNotSeeTableRecords([$passwordEvent]);
});

it('mostra no detalhe a versão publicada antes da alteração', function () {
    $original = Material::factory()->approved()->create(['title' => 'Título antigo', 'url' => 'https://example.com/antigo']);
    $revision = app(ReviseMaterial::class)->handle($original, ['title' => 'Título novo', 'description' => null, 'url' => 'https://example.com/novo', 'anonymous' => false]);
    app(ApproveMaterial::class)->handle($revision, $this->admin);
    $approval = AuditEvent::where('action', AuditAction::MaterialApproved)->sole();

    Livewire::test(ListAuditEvents::class)
        ->assertTableColumnStateSet('summary', 'Alteração', $approval)
        ->mountAction(TestAction::make('view')->table($approval))
        ->assertMountedActionModalSee(['Versão anterior', 'Título antigo', 'https://example.com/antigo']);
});

it('não oferece nenhuma ação que altere eventos', function () {
    app(RecordAuditEvent::class)->handle(AuditAction::PasswordChanged, $this->admin, $this->admin);
    $event = AuditEvent::sole();

    $page = Livewire::test(ListAuditEvents::class)->assertActionDoesNotExist('create');
    foreach (['edit', 'delete', 'forceDelete', 'restore'] as $action) {
        $page->assertActionDoesNotExist(TestAction::make($action)->table($event));
    }
});
