<?php

use App\Actions\ApproveMaterial;
use App\Actions\ReviseMaterial;
use App\Enums\Role;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\Opportunity;
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
            'kind' => 'material',
            'id' => $material->id,
            'code' => $material->code,
            'title' => 'Resumo de Grafos',
            'url' => 'https://example.com/grafos',
            'published_at' => $material->published_at->toIso8601String(),
            'discipline' => ['name' => 'Algoritmos I', 'slug' => $discipline->slug],
            'opportunity_type' => null,
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

it('busca por título, disciplina e autor, sem diferenciar acentos e maiúsculas', function (string $q) {
    $discipline = Discipline::where('name', 'Cálculo Diferencial e Integral I')->sole();
    $author = User::factory()->create(['display_name' => 'Joana Ribeiro']);
    $found = Material::factory()->for($discipline)->for($author, 'author')->approved()->create(['title' => 'Exercícios de limites']);
    $other = Material::factory()->for(Discipline::where('name', 'Algoritmos I')->sole())->approved()->create(['title' => 'Lista de recursão']);

    $ids = $this->actingAs(User::factory()->create())->getJson(FEED.'?q='.urlencode($q))->assertOk()->json('data.*.id');

    expect($ids)->toBe([$found->id]);
})->with(['exercicios', 'LIMITES', 'calculo diferencial', 'joana']);

it('não encontra material anônimo pelo nome do autor', function () {
    $author = User::factory()->create(['display_name' => 'Autora Sigilosa']);
    Material::factory()->for($author, 'author')->approved()->anonymous()->create(['title' => 'Resumo']);

    $this->actingAs(User::factory()->create())->getJson(FEED.'?q=sigilosa')->assertOk()->assertJsonCount(0, 'data');
    $this->actingAs(User::factory()->create())->getJson(FEED.'?q=resumo')->assertOk()->assertJsonCount(1, 'data');
});

it('trata % e _ da busca como texto', function () {
    Material::factory()->approved()->create(['title' => 'Qualquer coisa']);
    Material::factory()->approved()->create(['title' => 'Taxa de 100% de acerto']);

    $this->actingAs(User::factory()->create())->getJson(FEED.'?q='.urlencode('100%'))->assertOk()->assertJsonCount(1, 'data');
    $this->actingAs(User::factory()->create())->getJson(FEED.'?q='.urlencode('%'))->assertOk()->assertJsonCount(1, 'data');
    $this->actingAs(User::factory()->create())->getJson(FEED.'?q=_')->assertOk()->assertJsonCount(0, 'data');
});

it('ordena dos mais antigos, inclusive na segunda página', function () {
    foreach (range(1, 21) as $i) {
        Material::factory()->approved()->create(['published_at' => now()->subMinutes($i)]);
    }
    $user = User::factory()->create();

    $first = $this->actingAs($user)->getJson(FEED.'?order=oldest')->assertOk();
    $dates = $first->json('data.*.published_at');
    $second = $this->actingAs($user)->getJson(FEED.'?order=oldest&cursor='.$first->json('meta.next_cursor'))->assertOk()->assertJsonCount(1, 'data');

    expect($dates)->toBe(collect($dates)->sort()->values()->all())
        ->and($second->json('data.0.published_at') > end($dates))->toBeTrue();
});

it('valida a busca, a ordem e o tipo', function (string $query, string $field, string $message) {
    $this->actingAs(User::factory()->create())->getJson(FEED.'?'.$query)
        ->assertUnprocessable()
        ->assertJsonPath("errors.{$field}", [$message]);
})->with([
    'busca longa' => ['q='.str_repeat('a', 101), 'q', 'Use no máximo 100 caracteres na busca.'],
    'busca como lista' => ['q[]=a', 'q', 'O valor enviado precisa ser um texto.'],
    'ordem inválida' => ['order=popular', 'order', 'Escolha uma ordem válida.'],
    'tipo inválido' => ['type=post', 'type', 'Escolha um tipo válido.'],
]);

it('trata busca, ordem e tipo vazios como ausentes', function () {
    $this->actingAs(User::factory()->create())->getJson(FEED.'?q=&order=&type=')->assertOk();
});

it('conta materiais publicados e estudantes', function () {
    Material::factory()->count(2)->approved()->create();
    Material::factory()->create();
    Material::factory()->approved()->create()->delete();
    $original = Material::factory()->approved()->create();
    Material::factory()->approved()->create(['revises_id' => $original->id]);

    $this->actingAs(User::factory()->create())->getJson('/api/v1/community/stats')
        ->assertOk()
        ->assertExactJson(['materials' => 3, 'opportunities' => 0, 'students' => User::count()]);
});

it('exige sessão para os números', function () {
    $this->getJson('/api/v1/community/stats')->assertUnauthorized();
});

it('mistura materiais e oportunidades pela publicação, e a oportunidade não traz autoria', function () {
    $author = User::factory()->create(['display_name' => 'Pessoa Que Indicou']);
    $material = Material::factory()->approved()->create(['published_at' => now()->subHours(2)]);
    $opportunity = Opportunity::factory()->for($author, 'author')->approved()->create(['type' => 'ESTAGIO', 'title' => 'Estágio em dados', 'published_at' => now()->subHour()]);

    $response = $this->actingAs(User::factory()->create())->getJson(FEED)->assertOk()->assertJsonCount(2, 'data');

    expect($response->json('data.0'))->toMatchArray([
        'kind' => 'opportunity', 'id' => $opportunity->id, 'title' => 'Estágio em dados', 'opportunity_type' => 'ESTAGIO',
        'discipline' => null, 'author_name' => null, 'author_code' => null, 'is_mine' => false,
    ])
        ->and($response->json('data.1.kind'))->toBe('material')
        ->and($response->json('data.1.id'))->toBe($material->id)
        ->and($response->getContent())->not->toContain('Pessoa Que Indicou');
});

it('não mostra no mural a oportunidade vencida, excluída ou pendente', function () {
    Opportunity::factory()->approved()->create(['deadline' => now()->subDay()->toDateString()]);
    Opportunity::factory()->approved()->create()->delete();
    Opportunity::factory()->create();

    $this->actingAs(User::factory()->create())->getJson(FEED)->assertOk()->assertJsonCount(0, 'data');
});

it('filtra o mural por tipo e busca nas oportunidades', function () {
    $material = Material::factory()->approved()->create(['title' => 'Resumo de grafos']);
    $opportunity = Opportunity::factory()->approved()->create(['title' => 'Hackathon', 'description' => 'Maratona de grafos.']);
    $user = User::factory()->create();

    expect($this->actingAs($user)->getJson(FEED.'?type=material')->json('data.*.id'))->toBe([$material->id])
        ->and($this->actingAs($user)->getJson(FEED.'?type=opportunity')->json('data.*.id'))->toBe([$opportunity->id])
        ->and($this->actingAs($user)->getJson(FEED.'?q=maratona')->json('data.*.kind'))->toBe(['opportunity'])
        ->and($this->actingAs($user)->getJson(FEED.'?q=grafos')->json('data.*.kind'))->toEqualCanonicalizing(['material', 'opportunity']);
    $this->actingAs($user)->getJson(FEED.'?type=evento')->assertJsonValidationErrors(['type']);
});

it('pagina os dois tipos juntos sem repetir nem pular, mesmo publicados no mesmo instante', function () {
    $at = now()->subDay();
    $materials = Material::factory()->count(12)->approved()->create(['published_at' => $at]);
    $opportunities = Opportunity::factory()->count(12)->approved()->create(['published_at' => $at]);
    $user = User::factory()->create();

    $first = $this->actingAs($user)->getJson(FEED.'?order=oldest')->assertJsonCount(20, 'data');
    $second = $this->actingAs($user)->getJson(FEED.'?order=oldest&cursor='.$first->json('meta.next_cursor'))->assertJsonCount(4, 'data');

    $seen = collect([...$first->json('data'), ...$second->json('data')])->map(fn ($item) => $item['kind'].':'.$item['id']);
    $expected = $materials->map(fn ($m) => 'material:'.$m->id)->merge($opportunities->map(fn ($o) => 'opportunity:'.$o->id));
    expect($seen->unique()->count())->toBe(24)
        ->and($seen->sort()->values()->all())->toBe($expected->sort()->values()->all());
});

it('conta as oportunidades visíveis nos números', function () {
    Opportunity::factory()->count(2)->approved()->create();
    Opportunity::factory()->approved()->create(['deadline' => now()->subDay()->toDateString()]);
    Opportunity::factory()->create();

    $this->actingAs(User::factory()->create())->getJson('/api/v1/community/stats')->assertJsonPath('opportunities', 2);
});
