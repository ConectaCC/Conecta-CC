<?php

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

function shareUrl(?Discipline $discipline = null): string
{
    return '/api/v1/disciplines/'.($discipline ?? Discipline::where('name', 'Algoritmos I')->sole())->id.'/materials';
}

function sharePayload(array $overrides = []): array
{
    return array_merge(['title' => 'Lista de exercícios', 'description' => 'Com gabarito.', 'url' => 'https://site.com/lista', 'anonymous' => false], $overrides);
}

it('cria o material pendente em nome do autor', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(shareUrl(), sharePayload())->assertCreated();

    $material = Material::sole();
    expect($material->status)->toBe(MaterialStatus::Pendente)
        ->and($material->user_id)->toBe($user->id)
        ->and($material->anonymous)->toBeFalse()
        ->and($material->published_at)->toBeNull();
    $response->assertJsonPath('code', $material->code);
});

it('grava o envio anônimo', function () {
    $this->actingAs(User::factory()->create())->postJson(shareUrl(), sharePayload(['anonymous' => true]))->assertCreated();

    expect(Material::sole()->anonymous)->toBeTrue();
});

it('normaliza o link e a descrição vazia', function () {
    $this->actingAs(User::factory()->create())
        ->postJson(shareUrl(), sharePayload(['title' => '  Resumo  ', 'description' => '   ', 'url' => '  Site.com/lista  ']))
        ->assertCreated();

    expect(Material::sole())->title->toBe('Resumo')->description->toBeNull()->url->toBe('https://site.com/lista');
});

it('valida os campos com as mensagens da spec', function (array $payload, string $field, string $message) {
    $this->actingAs(User::factory()->create())
        ->postJson(shareUrl(), sharePayload($payload))
        ->assertUnprocessable()
        ->assertJsonPath("errors.{$field}.0", $message);
})->with([
    'título vazio' => [['title' => '  '], 'title', 'Informe um título para o material.'],
    'link vazio' => [['url' => ''], 'url', 'Informe o link do material.'],
    'link inválido' => [['url' => 'meu material'], 'url', 'Informe um link válido.'],
    'javascript' => [['url' => 'javascript:alert(1)'], 'url', 'Informe um link válido.'],
    'ftp' => [['url' => 'ftp://site.com/a'], 'url', 'Informe um link válido.'],
    'título longo' => [['title' => str_repeat('a', 201)], 'title', 'Use no máximo 200 caracteres.'],
    'descrição longa' => [['description' => str_repeat('a', 2001)], 'description', 'Use no máximo 2.000 caracteres.'],
    'link longo' => [['url' => 'https://site.com/'.str_repeat('a', 2040)], 'url', 'Use no máximo 2.048 caracteres.'],
]);

it('recusa o mesmo link escrito de outro jeito na mesma disciplina', function (MaterialStatus $status, string $typed) {
    Material::factory()->for(Discipline::where('name', 'Algoritmos I')->sole())
        ->create(['url' => 'https://site.com/x', 'status' => $status->value, 'published_at' => now()]);

    $this->actingAs(User::factory()->create())
        ->postJson(shareUrl(), sharePayload(['url' => $typed]))
        ->assertUnprocessable()
        ->assertJsonPath('errors.url.0', 'Esse link já foi compartilhado nesta disciplina.');
})->with([
    'pendente, sem protocolo' => [MaterialStatus::Pendente, 'site.com/x'],
    'aprovado, com espaços' => [MaterialStatus::Aprovado, '  https://site.com/x  '],
    'aprovado, host em maiúsculas' => [MaterialStatus::Aprovado, 'https://SITE.com/x'],
]);

it('aceita o link de novo quando o anterior não está ativo ou é de outra disciplina', function () {
    $algoritmos = Discipline::where('name', 'Algoritmos I')->sole();
    Material::factory()->for($algoritmos)->rejected('Quebrado.')->create(['url' => 'https://site.com/a']);
    Material::factory()->for($algoritmos)->create(['url' => 'https://site.com/b', 'status' => 'CANCELADO']);
    Material::factory()->for($algoritmos)->approved()->create(['url' => 'https://site.com/c'])->delete();
    Material::factory()->for(Discipline::where('name', 'Circuitos Digitais')->sole())->create(['url' => 'https://site.com/d']);
    $user = User::factory()->create();

    foreach (['a', 'b', 'c', 'd'] as $path) {
        $this->actingAs($user)->postJson(shareUrl(), sharePayload(['url' => "https://site.com/{$path}"]))->assertCreated();
    }
});

it('devolve 422 para payload malformado', function (array $payload) {
    $this->actingAs(User::factory()->create())->postJson(shareUrl(), sharePayload($payload))->assertUnprocessable();
})->with([
    'título como array' => [['title' => ['a']]],
    'link como número' => [['url' => 123]],
    'link como array' => [['url' => ['https://site.com']]],
    'anônimo como texto' => [['anonymous' => 'talvez']],
    'descrição como array' => [['description' => ['x']]],
]);

it('limita a 10 envios por hora', function () {
    $user = User::factory()->create();

    foreach (range(1, 10) as $i) {
        $this->actingAs($user)->postJson(shareUrl(), sharePayload(['url' => "https://site.com/{$i}"]))->assertCreated();
    }

    $this->actingAs($user)->postJson(shareUrl(), sharePayload(['url' => 'https://site.com/11']))
        ->assertTooManyRequests()
        ->assertJsonPath('message', fn (string $message) => str_starts_with($message, 'Muitas tentativas. Tente novamente em'));
});

it('exige login para compartilhar', function () {
    $this->postJson(shareUrl(), sharePayload())->assertUnauthorized();
});
