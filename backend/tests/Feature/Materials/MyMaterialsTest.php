<?php

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

const MINE = '/api/v1/me/materials';

/** @return array<string, Material> */
function myMaterialsOfEveryKind(User $user): array
{
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $make = fn (int $hoursAgo) => Material::factory()->for($discipline)->for($user, 'author')->state(['created_at' => now()->subHours($hoursAgo)]);

    $deleted = $make(1)->approved()->create();
    $deleted->delete();

    return [
        'pending' => $make(6)->create(),
        'approved' => $make(5)->approved()->create(),
        'rejected' => $make(4)->rejected('Link pede login.')->create(),
        'cancelled' => $make(3)->create(['status' => MaterialStatus::Cancelado->value]),
        'deleted' => $deleted,
    ];
}

it('lista só os próprios envios, mais recentes primeiro', function () {
    $user = User::factory()->create();
    $items = myMaterialsOfEveryKind($user);
    Material::factory()->create();

    $response = $this->actingAs($user)->getJson(MINE)->assertOk()->assertJsonCount(5, 'data');

    expect($response->json('data.*.id'))->toBe([
        $items['deleted']->id, $items['cancelled']->id, $items['rejected']->id, $items['approved']->id, $items['pending']->id,
    ]);
    $response->assertJsonPath('data.4.code', $items['pending']->code)
        ->assertJsonPath('data.4.discipline.slug', 'algoritmos-i');
});

it('mostra o status de cada situação, com Excluído para o soft delete', function () {
    $user = User::factory()->create();
    myMaterialsOfEveryKind($user);

    expect($this->actingAs($user)->getJson(MINE)->json('data.*.status'))
        ->toBe(['EXCLUIDO', 'CANCELADO', 'REJEITADO', 'APROVADO', 'PENDENTE']);
});

it('filtra por situação', function (string $filter, array $expected) {
    $user = User::factory()->create();
    $items = myMaterialsOfEveryKind($user);

    $ids = $this->actingAs($user)->getJson(MINE.'?status='.$filter)->assertOk()->json('data.*.id');

    expect($ids)->toBe(array_map(fn (string $key) => $items[$key]->id, $expected));
})->with([
    'em andamento' => ['pending', ['pending']],
    'aprovados' => ['approved', ['approved']],
    'rejeitados' => ['rejected', ['rejected']],
    'excluídos' => ['deleted', ['deleted']],
    'todos' => ['all', ['deleted', 'cancelled', 'rejected', 'approved', 'pending']],
    'valor inválido vira todos' => ['qualquer', ['deleted', 'cancelled', 'rejected', 'approved', 'pending']],
]);

it('traz o motivo da rejeição', function () {
    $user = User::factory()->create();
    myMaterialsOfEveryKind($user);

    $this->actingAs($user)->getJson(MINE.'?status=rejected')->assertJsonPath('data.0.rejection_reason', 'Link pede login.');
});

it('mostra as alterações dentro do original, não como itens', function () {
    $user = User::factory()->create();
    $original = Material::factory()->for($user, 'author')->approved()->create();
    Material::factory()->for($user, 'author')->for($original->discipline)->rejected('Fora do tema.')
        ->create(['revises_id' => $original->id, 'decided_at' => now()->subDay()]);

    $this->actingAs($user)->getJson(MINE)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.pending_revision', null)
        ->assertJsonPath('data.0.rejected_revision.reason', 'Fora do tema.');

    Material::factory()->for($user, 'author')->for($original->discipline)
        ->create(['revises_id' => $original->id, 'title' => 'Título novo']);

    $this->actingAs($user)->getJson(MINE)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.pending_revision.title', 'Título novo')
        ->assertJsonPath('data.0.rejected_revision', null);
});

it('pagina de 20 em 20, com o filtro em todas as páginas', function () {
    $user = User::factory()->create();
    foreach (range(1, 21) as $i) {
        Material::factory()->for($user, 'author')->state(['created_at' => now()->subMinutes($i)])->create();
    }
    Material::factory()->for($user, 'author')->approved()->create();

    $first = $this->actingAs($user)->getJson(MINE.'?status=pending')->assertJsonCount(20, 'data');
    $cursor = $first->json('meta.next_cursor');
    expect($cursor)->not->toBeNull();

    $this->actingAs($user)->getJson(MINE.'?status=pending&cursor='.$cursor)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'PENDENTE')
        ->assertJsonPath('meta.next_cursor', null);
});

it('exige login', function () {
    $this->getJson(MINE)->assertUnauthorized();
});
