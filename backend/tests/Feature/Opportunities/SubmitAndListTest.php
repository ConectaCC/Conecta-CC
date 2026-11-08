<?php

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\AuditEvent;
use App\Models\Opportunity;
use App\Models\User;

const OPPORTUNITIES = '/api/v1/opportunities';

function opportunityPayload(array $overrides = []): array
{
    return [
        'type' => 'ESTAGIO',
        'title' => 'Estágio em desenvolvimento web',
        'description' => 'Vaga para estudantes a partir do 3º semestre.',
        'url' => 'https://empresa.com/vagas/estagio',
        'modality' => 'HIBRIDO',
        'deadline' => null,
        ...$overrides,
    ];
}

it('envia a oportunidade como pendente, em nome de quem enviou, e registra na auditoria', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload(['url' => 'empresa.com/vaga']))->assertCreated();

    $opportunity = Opportunity::sole();
    expect($opportunity->status)->toBe(MaterialStatus::Pendente)
        ->and($opportunity->user_id)->toBe($user->id)
        ->and($opportunity->url)->toBe('https://empresa.com/vaga')
        ->and($opportunity->published_at)->toBeNull()
        ->and($opportunity->code)->toBe(sprintf('OPO-%05d', $opportunity->id));
    $response->assertJsonPath('code', $opportunity->code)->assertJsonMissingPath('author_name');

    $event = AuditEvent::sole();
    expect($event->action)->toBe(AuditAction::OpportunitySubmitted)
        ->and($event->actor_id)->toBe($user->id)
        ->and($event->opportunity_id)->toBe($opportunity->id)
        ->and($event->data)->toEqual(['title' => 'Estágio em desenvolvimento web', 'type' => 'ESTAGIO']);
});

it('valida cada campo com a mensagem certa', function (array $overrides, string $field, string $message) {
    $this->actingAs(User::factory()->create())->postJson(OPPORTUNITIES, opportunityPayload($overrides))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field => $message]);

    expect(Opportunity::count())->toBe(0);
})->with([
    'sem tipo' => [['type' => null], 'type', 'Escolha o tipo da oportunidade.'],
    'tipo inválido' => [['type' => 'BOLSA'], 'type', 'Escolha o tipo da oportunidade.'],
    'sem título' => [['title' => '  '], 'title', 'Informe um título para a oportunidade.'],
    'título longo' => [['title' => str_repeat('a', 201)], 'title', 'Use no máximo 200 caracteres.'],
    'sem descrição' => [['description' => ''], 'description', 'Descreva a oportunidade em poucas palavras.'],
    'descrição longa' => [['description' => str_repeat('a', 301)], 'description', 'Use no máximo 300 caracteres.'],
    'sem link' => [['url' => ''], 'url', 'Informe o link da oportunidade.'],
    'link inválido' => [['url' => 'javascript:alert(1)'], 'url', 'Informe um link válido.'],
    'sem modalidade' => [['modality' => null], 'modality', 'Escolha a modalidade.'],
    'modalidade inválida' => [['modality' => 'ONLINE'], 'modality', 'Escolha a modalidade.'],
    'prazo passado' => [['deadline' => '2020-01-01'], 'deadline', 'Escolha uma data a partir de hoje.'],
    'prazo inválido' => [['deadline' => 'amanhã'], 'deadline', 'Informe uma data válida.'],
]);

it('aceita o prazo de hoje no horário de Brasília', function () {
    $this->travelTo('2026-11-09 01:00:00'); // UTC: ainda é dia 08 em Brasília

    $this->actingAs(User::factory()->create())->postJson(OPPORTUNITIES, opportunityPayload(['deadline' => '2026-11-08']))->assertCreated();
});

it('recusa um link que já está em outra oportunidade ativa', function () {
    Opportunity::factory()->approved()->create(['url' => 'https://empresa.com/vagas/estagio']);
    Opportunity::factory()->create(['url' => 'https://empresa.com/pendente']);
    Opportunity::factory()->rejected('Não.')->create(['url' => 'https://empresa.com/rejeitada']);

    $user = User::factory()->create();
    $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload())->assertJsonValidationErrors(['url' => 'Essa oportunidade já foi compartilhada.']);
    $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload(['url' => 'https://empresa.com/pendente']))->assertJsonValidationErrors(['url']);
    $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload(['url' => 'https://empresa.com/rejeitada']))->assertCreated();
});

it('limita os envios por hora, junto com os materiais', function () {
    $user = User::factory()->create();
    foreach (range(1, 10) as $i) {
        $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload(['url' => "https://empresa.com/{$i}"]))->assertCreated();
    }

    $this->actingAs($user)->postJson(OPPORTUNITIES, opportunityPayload(['url' => 'https://empresa.com/11']))->assertTooManyRequests();
});

it('lista só as aprovadas, não excluídas e dentro do prazo, sem autoria', function () {
    $visible = Opportunity::factory()->approved()->create(['title' => 'Visível']);
    Opportunity::factory()->create();
    Opportunity::factory()->rejected('Não.')->create();
    Opportunity::factory()->approved()->create()->delete();
    Opportunity::factory()->approved()->create(['deadline' => now()->subDay()->toDateString()]);

    $response = $this->actingAs(User::factory()->create())->getJson(OPPORTUNITIES)->assertOk()->assertJsonCount(1, 'data');

    $response->assertJsonPath('data.0.id', $visible->id);
    expect(array_keys($response->json('data.0')))->toEqualCanonicalizing(['id', 'code', 'type', 'title', 'description', 'url', 'modality', 'deadline', 'published_at'])
        ->and($response->getContent())->not->toContain($visible->author->display_name);
});

it('mantém visível até o fim do último dia do prazo, em Brasília', function () {
    $this->travelTo('2026-11-08 12:00:00');
    $opportunity = Opportunity::factory()->approved()->create(['deadline' => '2026-11-10']);
    $user = User::factory()->create();

    $this->travelTo('2026-11-11 02:00:00'); // 23h do dia 10 em Brasília
    $this->actingAs($user)->getJson(OPPORTUNITIES)->assertJsonPath('data.0.id', $opportunity->id);

    $this->travelTo('2026-11-11 03:30:00'); // 00h30 do dia 11 em Brasília
    $this->actingAs($user)->getJson(OPPORTUNITIES)->assertJsonCount(0, 'data');
});

it('sem prazo, fica visível por 15 dias depois da aprovação', function () {
    $this->travelTo('2026-11-01 15:00:00');
    $opportunity = Opportunity::factory()->approved()->create(['deadline' => null]);
    $user = User::factory()->create();

    $this->travelTo('2026-11-16 15:00:00');
    $this->actingAs($user)->getJson(OPPORTUNITIES)->assertJsonPath('data.0.id', $opportunity->id)->assertJsonPath('data.0.deadline', null);

    $this->travelTo('2026-11-17 15:00:00');
    $this->actingAs($user)->getJson(OPPORTUNITIES)->assertJsonCount(0, 'data');
});

it('busca sem acentos no título e na descrição, e filtra por tipo', function () {
    $estagio = Opportunity::factory()->approved()->create(['type' => 'ESTAGIO', 'title' => 'Estágio em Programação', 'description' => 'Back-end.']);
    $evento = Opportunity::factory()->approved()->create(['type' => 'EVENTO', 'title' => 'Hackathon', 'description' => 'Maratona de programação.']);
    $user = User::factory()->create();

    expect($this->actingAs($user)->getJson(OPPORTUNITIES.'?q=PROGRAMACAO')->json('data.*.id'))->toEqualCanonicalizing([$estagio->id, $evento->id])
        ->and($this->actingAs($user)->getJson(OPPORTUNITIES.'?q=estagio')->json('data.*.id'))->toBe([$estagio->id])
        ->and($this->actingAs($user)->getJson(OPPORTUNITIES.'?type=EVENTO')->json('data.*.id'))->toBe([$evento->id]);
    $this->actingAs($user)->getJson(OPPORTUNITIES.'?type=BOLSA')->assertJsonValidationErrors(['type']);
});

it('ordena pela publicação e pagina de 20 em 20 sem repetir', function () {
    foreach (range(1, 21) as $i) {
        Opportunity::factory()->approved()->create(['published_at' => now()->subMinutes($i)]);
    }
    $user = User::factory()->create();

    $first = $this->actingAs($user)->getJson(OPPORTUNITIES)->assertJsonCount(20, 'data');
    $second = $this->actingAs($user)->getJson(OPPORTUNITIES.'?cursor='.$first->json('meta.next_cursor'))->assertJsonCount(1, 'data');

    expect(array_intersect($first->json('data.*.id'), $second->json('data.*.id')))->toBe([]);
});

it('exige sessão', function () {
    $this->getJson(OPPORTUNITIES)->assertUnauthorized();
    $this->postJson(OPPORTUNITIES, opportunityPayload())->assertUnauthorized();
});
