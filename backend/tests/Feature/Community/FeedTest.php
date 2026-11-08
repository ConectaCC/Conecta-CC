<?php

use App\Actions\ApproveMaterial;
use App\Actions\ReviseMaterial;
use App\Enums\Role;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

const FEED = '/api/v1/community/feed';

it('lista só os originais aprovados e não excluídos, do mais recente para o mais antigo', function () {
    $old = Material::factory()->approved()->create(['published_at' => now()->subDays(3)]);
    $new = Material::factory()->approved()->create(['published_at' => now()->subDay()]);
    Material::factory()->create();
    Material::factory()->rejected('Não.')->create();
    Material::factory()->create(['status' => 'CANCELADO']);
    Material::factory()->approved()->create()->delete();
    Material::factory()->approved()->create(['revises_id' => $old->id]);

    $this->actingAs(User::factory()->create())->getJson(FEED)
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $new->id)
        ->assertJsonPath('data.1.id', $old->id);
});

it('traz a disciplina, o autor e o link do material identificado', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $author = User::factory()->create(['display_name' => 'Marcos']);
    $material = Material::factory()->for($discipline)->for($author, 'author')->approved()->create(['title' => 'Resumo de Grafos', 'url' => 'https://example.com/grafos']);

    $this->actingAs(User::factory()->create())->getJson(FEED)
        ->assertOk()
        ->assertJsonPath('data.0', [
            'id' => $material->id,
            'code' => $material->code,
            'title' => 'Resumo de Grafos',
            'url' => 'https://example.com/grafos',
            'published_at' => $material->published_at->toIso8601String(),
            'discipline' => ['name' => 'Algoritmos I', 'slug' => $discipline->slug],
            'author_name' => 'Marcos',
            'author_code' => $author->fresh()->public_code,
            'is_mine' => false,
        ]);
});

it('não envia o autor do material anônimo para nenhum papel, nem para o próprio autor', function (?Role $role) {
    $author = User::factory()->create(['display_name' => 'Autora Sigilosa']);
    Material::factory()->for($author, 'author')->approved()->anonymous()->create();
    $viewer = $role === null ? $author : User::factory()->create(['role' => $role]);

    $response = $this->actingAs($viewer)->getJson(FEED)->assertOk();

    expect($response->json('data.0'))->toMatchArray(['author_name' => null, 'author_code' => null, 'is_mine' => false])
        ->and($response->getContent())->not->toContain('Autora Sigilosa', $author->fresh()->public_code);
})->with([
    'ALUNO' => [Role::Aluno],
    'MODERADOR' => [Role::Moderador],
    'ADMINISTRADOR' => [Role::Administrador],
    'o próprio autor' => [null],
]);

it('marca como meu só o material identificado da própria pessoa', function () {
    $me = User::factory()->create();
    Material::factory()->for($me, 'author')->approved()->create();

    $this->actingAs($me)->getJson(FEED)->assertOk()->assertJsonPath('data.0.is_mine', true);
});

it('mostra o título novo depois de uma alteração aprovada, sem nova publicação', function () {
    $original = Material::factory()->approved()->create(['title' => 'Antigo']);
    $revision = app(ReviseMaterial::class)->handle($original, ['title' => 'Novo', 'description' => null, 'url' => 'https://example.com/novo', 'anonymous' => false]);
    app(ApproveMaterial::class)->handle($revision, User::factory()->create(['role' => Role::Moderador]));

    $this->actingAs(User::factory()->create())->getJson(FEED)
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $original->id)
        ->assertJsonPath('data.0.title', 'Novo');
});

it('pagina de 20 em 20 por cursor, sem repetir itens', function () {
    foreach (range(1, 21) as $i) {
        Material::factory()->approved()->create(['published_at' => now()->subMinutes($i)]);
    }
    $user = User::factory()->create();

    $first = $this->actingAs($user)->getJson(FEED)->assertOk()->assertJsonCount(20, 'data');
    $cursor = $first->json('meta.next_cursor');
    // um material novo aprovado enquanto a pessoa lê não desloca a segunda página
    Material::factory()->approved()->create(['published_at' => now()]);
    $second = $this->actingAs($user)->getJson(FEED.'?cursor='.$cursor)->assertOk()->assertJsonCount(1, 'data');

    expect(array_intersect($first->json('data.*.id'), $second->json('data.*.id')))->toBe([])
        ->and($second->json('meta.next_cursor'))->toBeNull();
});

it('exige sessão', function () {
    $this->getJson(FEED)->assertUnauthorized();
});
