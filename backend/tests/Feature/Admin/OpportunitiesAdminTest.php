<?php

use App\Actions\ApproveOpportunity;
use App\Actions\CreateOpportunity;
use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Filament\Resources\AuditEvents\Pages\ListAuditEvents;
use App\Filament\Resources\Opportunities\Pages\ListOpportunities;
use App\Models\AuditEvent;
use App\Models\Opportunity;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->create(['role' => Role::Administrador]);
    $this->actingAs($this->admin, 'web');
});

it('fica fechada para quem não é ADMINISTRADOR', function (Role $role) {
    $this->actingAs(User::factory()->create(['role' => $role]), 'web')->get('/admin/oportunidades')->assertForbidden();
})->with([Role::Aluno, Role::Moderador]);

it('lista todas as situações com quem enviou', function () {
    $author = User::factory()->create(['display_name' => 'Pessoa Que Indicou']);
    $pending = Opportunity::factory()->for($author, 'author')->create(['title' => 'Pendente']);
    $deleted = Opportunity::factory()->approved()->create();
    $deleted->delete();
    $this->travelTo(now()->addMonth());
    $expired = Opportunity::factory()->approved()->create(['published_at' => now()->subMonth()]);

    Livewire::test(ListOpportunities::class)
        ->assertCanSeeTableRecords([$pending, $deleted, $expired])
        ->assertSee('Pessoa Que Indicou')
        ->assertTableColumnStateSet('situation', 'Pendente', $pending)
        ->assertTableColumnStateSet('situation', 'Excluída', $deleted)
        ->assertTableColumnStateSet('situation', 'Encerrada', $expired);
});

it('não oferece ação que altere dados', function () {
    $opportunity = Opportunity::factory()->create();

    $page = Livewire::test(ListOpportunities::class)->assertActionDoesNotExist('create');
    foreach (['edit', 'delete', 'approve', 'reject'] as $action) {
        $page->assertActionDoesNotExist(TestAction::make($action)->table($opportunity));
    }
    $page->mountAction(TestAction::make('view')->table($opportunity))->assertMountedActionModalSee([$opportunity->title, $opportunity->url]);
});

it('a auditoria mostra a oportunidade e o tipo', function () {
    $author = User::factory()->create();
    $opportunity = app(CreateOpportunity::class)->handle($author, ['type' => 'EVENTO', 'title' => 'Hackathon X', 'description' => 'Maratona.', 'url' => 'https://example.com/hx', 'modality' => 'PRESENCIAL', 'deadline' => null]);
    app(ApproveOpportunity::class)->handle($opportunity, $this->admin);
    $approved = AuditEvent::where('action', AuditAction::OpportunityApproved)->sole();

    Livewire::test(ListAuditEvents::class)
        ->assertTableColumnStateSet('action', 'Oportunidade aprovada', $approved)
        ->assertTableColumnStateSet('subject', $opportunity->code.' · Hackathon X', $approved)
        ->assertTableColumnStateSet('summary', 'Evento', $approved);
    expect($opportunity->fresh()->status)->toBe(MaterialStatus::Aprovado);
});

it('mostra na lista o código e o tipo, a data de envio e quem decidiu', function () {
    $this->travelTo('2026-11-08 12:00:00');
    $moderator = User::factory()->create(['role' => Role::Moderador, 'display_name' => 'Marcos Moderador']);
    $opportunity = Opportunity::factory()->approved()->create(['type' => 'ESTAGIO', 'decided_by' => $moderator->id, 'decided_at' => now()])->refresh();

    Livewire::test(ListOpportunities::class)
        ->assertSee($opportunity->code.' · Estágio')
        ->assertTableColumnExists('created_at')
        ->assertTableColumnStateSet('decider.display_name', 'Marcos Moderador', $opportunity);
});

it('filtra pelo tipo', function () {
    $internship = Opportunity::factory()->create(['type' => 'ESTAGIO']);
    $event = Opportunity::factory()->create(['type' => 'EVENTO']);

    Livewire::test(ListOpportunities::class)
        ->filterTable('type', 'EVENTO')
        ->assertCanSeeTableRecords([$event])
        ->assertCanNotSeeTableRecords([$internship]);
});

it('mostra no detalhe todos os dados da oportunidade', function () {
    $this->travelTo('2026-11-08 12:00:00');
    $author = User::factory()->create(['display_name' => 'Pessoa Que Indicou']);
    $moderator = User::factory()->create(['role' => Role::Moderador, 'display_name' => 'Marcos Moderador']);
    $rejected = Opportunity::factory()->for($author, 'author')->rejected('Link quebrado.')->create([
        'type' => 'ESTAGIO', 'modality' => 'REMOTO', 'deadline' => '2026-12-01', 'title' => 'Vaga em web',
        'description' => 'Vaga para o 3º semestre.', 'url' => 'https://empresa.com/vaga', 'decided_by' => $moderator->id, 'decided_at' => now(),
    ])->refresh();

    Livewire::test(ListOpportunities::class)
        ->mountAction(TestAction::make('view')->table($rejected))
        ->assertMountedActionModalSee([
            $rejected->code, 'Vaga em web', 'Estágio', 'Remoto', '01/12/2026', 'Vaga para o 3º semestre.', 'https://empresa.com/vaga',
            'Pessoa Que Indicou', 'Rejeitada', 'Motivo da rejeição', 'Link quebrado.', 'Enviada em', 'Marcos Moderador', 'Decidida em',
        ]);

    Livewire::test(ListOpportunities::class)
        ->mountAction(TestAction::make('view')->table(Opportunity::factory()->create()))
        ->assertMountedActionModalDontSee('Motivo da rejeição');
});
