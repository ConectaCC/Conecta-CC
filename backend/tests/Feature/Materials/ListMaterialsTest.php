<?php

use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

function listUrl(Discipline $discipline): string
{
    return "/api/v1/disciplines/{$discipline->id}/materials";
}

it('lista só os materiais aprovados, do mais recente para o mais antigo', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $old = Material::factory()->for($discipline)->approved()->create(['published_at' => now()->subDays(3)]);
    $new = Material::factory()->for($discipline)->approved()->create(['published_at' => now()->subDay()]);
    Material::factory()->for($discipline)->create();
    Material::factory()->for($discipline)->rejected('Não.')->create();
    Material::factory()->for($discipline)->approved()->create()->delete();
    Material::factory()->for($discipline)->approved()->create(['revises_id' => $old->id]);
    Material::factory()->for(Discipline::where('name', 'Circuitos Digitais')->sole())->approved()->create();

    $this->actingAs(User::factory()->create())->getJson(listUrl($discipline))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $new->id)
        ->assertJsonPath('data.1.id', $old->id)
        ->assertJsonPath('data.0.code', $new->code);
});

it('pagina de 20 em 20', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    foreach (range(1, 21) as $i) {
        Material::factory()->for($discipline)->approved()->create(['published_at' => now()->subMinutes($i)]);
    }
    $user = User::factory()->create();

    $first = $this->actingAs($user)->getJson(listUrl($discipline))->assertOk()->assertJsonCount(20, 'data');
    $cursor = $first->json('meta.next_cursor');
    expect($cursor)->not->toBeNull();

    $this->actingAs($user)->getJson(listUrl($discipline).'?cursor='.$cursor)
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('meta.next_cursor', null);
});

it('mostra o nome do autor só quando não é anônimo', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $visible = User::factory()->create(['display_name' => 'Ana Visível']);
    $hidden = User::factory()->create(['display_name' => 'Bruno Escondido']);
    Material::factory()->for($discipline)->for($visible, 'author')->approved()->create(['published_at' => now()]);
    Material::factory()->for($discipline)->for($hidden, 'author')->approved()->anonymous()->create(['published_at' => now()->subDay()]);

    $this->actingAs(User::factory()->create())->getJson(listUrl($discipline))
        ->assertJsonPath('data.0.author_name', 'Ana Visível')
        ->assertJsonPath('data.1.author_name', null)
        ->assertDontSee('Bruno Escondido');
});

it('marca is_mine só para o autor', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $author = User::factory()->create();
    Material::factory()->for($discipline)->for($author, 'author')->approved()->anonymous()->create();

    $this->actingAs($author)->getJson(listUrl($discipline))->assertJsonPath('data.0.is_mine', true);
    $this->actingAs(User::factory()->create())->getJson(listUrl($discipline))->assertJsonPath('data.0.is_mine', false);
});

it('exige login para listar', function () {
    $this->getJson(listUrl(Discipline::first()))->assertUnauthorized();
});
