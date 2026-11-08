<?php

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Models\AuditEvent;
use App\Models\Opportunity;
use App\Models\User;

function opportunityModerator(Role $role = Role::Moderador): User
{
    return User::factory()->create(['role' => $role]);
}

it('a fila mostra as pendentes com quem enviou, menos as do próprio MODERADOR', function () {
    $moderator = opportunityModerator();
    $author = User::factory()->create(['display_name' => 'Beatriz Souza']);
    $pending = Opportunity::factory()->for($author, 'author')->create();
    Opportunity::factory()->for($moderator, 'author')->create();
    Opportunity::factory()->approved()->create();

    $this->actingAs($moderator)->getJson('/api/v1/moderation/opportunities')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $pending->id)
        ->assertJsonPath('0.author_name', 'Beatriz Souza')
        ->assertJsonPath('0.is_own', false);
});

it('o ADMINISTRADOR vê também a própria', function () {
    $admin = opportunityModerator(Role::Administrador);
    Opportunity::factory()->for($admin, 'author')->create();

    $this->actingAs($admin)->getJson('/api/v1/moderation/opportunities')->assertJsonCount(1)->assertJsonPath('0.is_own', true);
});

it('ALUNO não acessa a fila nem decide', function () {
    $opportunity = Opportunity::factory()->create();
    $student = User::factory()->create();

    $this->actingAs($student)->getJson('/api/v1/moderation/opportunities')->assertForbidden();
    $this->actingAs($student)->postJson("/api/v1/moderation/opportunities/{$opportunity->id}/approve")->assertForbidden();
});

it('aprova, publica, notifica quem enviou e registra na auditoria', function () {
    $moderator = opportunityModerator();
    $opportunity = Opportunity::factory()->create(['title' => 'Estágio em dados']);

    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$opportunity->id}/approve")->assertOk();

    $fresh = $opportunity->fresh();
    expect($fresh->status)->toBe(MaterialStatus::Aprovado)
        ->and($fresh->published_at)->not->toBeNull()
        ->and($fresh->decided_by)->toBe($moderator->id);
    $this->actingAs($opportunity->author)->getJson('/api/v1/notifications')
        ->assertJsonPath('data.0.title', 'Sua oportunidade foi aprovada')
        ->assertJsonPath('data.0.detail', 'A oportunidade “Estágio em dados” foi aprovada.')
        ->assertJsonPath('data.0.link', "/oportunidade#oportunidade-{$opportunity->id}");
    $event = AuditEvent::sole();
    expect([$event->action, $event->actor_id, $event->opportunity_id])->toBe([AuditAction::OpportunityApproved, $moderator->id, $opportunity->id]);
});

it('rejeita com motivo, notifica com o link de Meus conteúdos e registra na auditoria', function () {
    $moderator = opportunityModerator();
    $opportunity = Opportunity::factory()->create(['title' => 'Vaga antiga']);

    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$opportunity->id}/reject", ['reason' => ''])
        ->assertJsonValidationErrors(['reason' => 'Informe o motivo da rejeição.']);
    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$opportunity->id}/reject", ['reason' => 'Vaga encerrada.'])->assertOk();

    expect($opportunity->fresh()->status)->toBe(MaterialStatus::Rejeitado)
        ->and($opportunity->fresh()->rejection_reason)->toBe('Vaga encerrada.');
    $this->actingAs($opportunity->author)->getJson('/api/v1/notifications')
        ->assertJsonPath('data.0.title', 'Sua oportunidade não foi aprovada')
        ->assertJsonPath('data.0.link', "/perfil/meus-conteudos?tipo=oportunidades&item={$opportunity->code}");
    expect(AuditEvent::sole()->action)->toBe(AuditAction::OpportunityRejected);
});

it('o MODERADOR não decide sobre a própria oportunidade', function () {
    $moderator = opportunityModerator();
    $own = Opportunity::factory()->for($moderator, 'author')->create();

    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$own->id}/approve")->assertForbidden();
    expect($own->fresh()->status)->toBe(MaterialStatus::Pendente);
});

it('decisão que chega tarde recebe 409 e não grava nada', function () {
    $moderator = opportunityModerator();
    $cancelled = Opportunity::factory()->create(['status' => 'CANCELADO']);
    $decided = Opportunity::factory()->approved()->create();

    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$cancelled->id}/approve")
        ->assertConflict()->assertJsonPath('message', 'Esta oportunidade foi cancelada por quem enviou.');
    $this->actingAs($moderator)->postJson("/api/v1/moderation/opportunities/{$decided->id}/reject", ['reason' => 'Não.'])
        ->assertConflict()->assertJsonPath('message', 'Esta oportunidade já foi analisada.');
    expect(AuditEvent::count())->toBe(0);
});

it('aprovar depois do prazo informado é aceito, mas a oportunidade já nasce encerrada', function () {
    $this->travelTo('2026-11-01 12:00:00');
    $opportunity = Opportunity::factory()->create(['deadline' => '2026-11-05']);
    $this->travelTo('2026-11-10 12:00:00');

    $this->actingAs(opportunityModerator())->postJson("/api/v1/moderation/opportunities/{$opportunity->id}/approve")->assertOk();

    $this->actingAs(User::factory()->create())->getJson('/api/v1/opportunities')->assertJsonCount(0, 'data');
    $this->actingAs($opportunity->author)->getJson('/api/v1/me/opportunities')->assertJsonPath('data.0.status', 'ENCERRADO');
});
