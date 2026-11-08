<?php

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\AuditEvent;
use App\Models\Opportunity;
use App\Models\User;

it('o autor cancela o envio pendente, com auditoria', function () {
    $opportunity = Opportunity::factory()->create();

    $this->actingAs($opportunity->author)->postJson("/api/v1/opportunities/{$opportunity->id}/cancel")
        ->assertOk()
        ->assertJsonPath('status', 'CANCELADO');

    expect($opportunity->fresh()->status)->toBe(MaterialStatus::Cancelado);
    $event = AuditEvent::sole();
    expect([$event->action, $event->actor_id, $event->opportunity_id])->toBe([AuditAction::OpportunityCancelled, $opportunity->user_id, $opportunity->id]);
});

it('não cancela o que já foi decidido nem o de outra pessoa', function () {
    $approved = Opportunity::factory()->approved()->create();
    $pending = Opportunity::factory()->create();

    $this->actingAs($approved->author)->postJson("/api/v1/opportunities/{$approved->id}/cancel")
        ->assertConflict()->assertJsonPath('message', 'Este envio não pode ser cancelado.');
    $this->actingAs(User::factory()->create())->postJson("/api/v1/opportunities/{$pending->id}/cancel")->assertForbidden();

    expect(AuditEvent::count())->toBe(0);
});

it('o autor exclui a aprovada, que some da página, com auditoria', function () {
    $opportunity = Opportunity::factory()->approved()->create();

    $this->actingAs($opportunity->author)->deleteJson("/api/v1/opportunities/{$opportunity->id}")->assertNoContent();

    expect($opportunity->fresh()->trashed())->toBeTrue();
    $this->actingAs(User::factory()->create())->getJson('/api/v1/opportunities')->assertJsonCount(0, 'data');
    expect(AuditEvent::sole()->action)->toBe(AuditAction::OpportunityDeleted);
});

it('não exclui a pendente nem a de outra pessoa', function () {
    $pending = Opportunity::factory()->create();
    $approved = Opportunity::factory()->approved()->create();

    $this->actingAs($pending->author)->deleteJson("/api/v1/opportunities/{$pending->id}")
        ->assertConflict()->assertJsonPath('message', 'Esta oportunidade não pode ser excluída.');
    $this->actingAs(User::factory()->create())->deleteJson("/api/v1/opportunities/{$approved->id}")->assertForbidden();
});

it('lista as próprias oportunidades em todas as situações, inclusive encerrada', function () {
    $me = User::factory()->create();
    $this->travelTo('2026-11-01 12:00:00');
    $expired = Opportunity::factory()->for($me, 'author')->approved()->create(['title' => 'Encerrada']);
    $this->travelTo('2026-11-20 12:00:00');
    $pending = Opportunity::factory()->for($me, 'author')->create();
    $rejected = Opportunity::factory()->for($me, 'author')->rejected('Link quebrado.')->create();
    $deleted = Opportunity::factory()->for($me, 'author')->approved()->create();
    $deleted->delete();
    Opportunity::factory()->create();

    $data = collect($this->actingAs($me)->getJson('/api/v1/me/opportunities')->assertOk()->json('data'))->keyBy('id');

    expect($data)->toHaveCount(4)
        ->and($data[$expired->id]['status'])->toBe('ENCERRADO')
        ->and($data[$pending->id]['status'])->toBe('PENDENTE')
        ->and($data[$rejected->id]['status'])->toBe('REJEITADO')
        ->and($data[$rejected->id]['rejection_reason'])->toBe('Link quebrado.')
        ->and($data[$deleted->id]['status'])->toBe('EXCLUIDO');

    expect($this->actingAs($me)->getJson('/api/v1/me/opportunities?status=pending')->json('data.*.id'))->toBe([$pending->id])
        ->and($this->actingAs($me)->getJson('/api/v1/me/opportunities?status=deleted')->json('data.*.id'))->toBe([$deleted->id])
        ->and($this->actingAs($me)->getJson('/api/v1/me/opportunities?status=approved')->json('data.*.id'))->toBe([$expired->id])
        ->and($this->actingAs($me)->getJson('/api/v1/me/opportunities?status=rejected')->json('data.*.id'))->toBe([$rejected->id]);
});

it('encerra só a aprovada, e só depois do último dia do prazo', function (string $status, string $deadline, string $expected) {
    $this->travelTo('2026-11-08 12:00:00');
    $me = User::factory()->create();
    $factory = Opportunity::factory()->for($me, 'author');
    ($status === 'APROVADO' ? $factory->approved() : $factory)->create(['deadline' => $deadline]);

    $this->actingAs($me)->getJson('/api/v1/me/opportunities')->assertJsonPath('data.0.status', $expected);
})->with([
    'aprovada, prazo ontem' => ['APROVADO', '2026-11-07', 'ENCERRADO'],
    'aprovada, prazo hoje' => ['APROVADO', '2026-11-08', 'APROVADO'],
    'pendente, prazo ontem' => ['PENDENTE', '2026-11-07', 'PENDENTE'],
]);

it('pagina Minhas oportunidades de 20 em 20', function () {
    $me = User::factory()->create();
    Opportunity::factory()->for($me, 'author')->count(21)->create();

    $first = $this->actingAs($me)->getJson('/api/v1/me/opportunities')->assertJsonCount(20, 'data');
    $this->actingAs($me)->getJson('/api/v1/me/opportunities?cursor='.$first->json('meta.next_cursor'))->assertJsonCount(1, 'data');
});
