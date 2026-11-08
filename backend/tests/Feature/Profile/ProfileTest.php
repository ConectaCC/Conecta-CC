<?php

use App\Models\Discipline;
use App\Models\User;

function profilePayload(array $overrides = []): array
{
    return array_merge([
        'display_name' => 'Ana Silva',
        'discipline_ids' => [Discipline::where('name', 'Algoritmos I')->value('id')],
        'about' => null,
        'interests' => [],
        'can_help_with' => [],
        'needs_help_with' => [],
        'github_url' => null,
        'linkedin_url' => null,
    ], $overrides);
}

it('conta nova tem Perfil incompleto', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/api/v1/me')->assertJsonPath('profile_complete', false);
    $this->actingAs($user)->getJson('/api/v1/profile')->assertOk()
        ->assertJsonPath('complete', false)
        ->assertJsonPath('disciplines', [])
        ->assertJsonPath('interests', []);
});

it('salva o Perfil e fica completo', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->putJson('/api/v1/profile', profilePayload([
        'display_name' => '  Ana   Maria  Silva ',
        'about' => "Linha 1\nLinha 2",
        'interests' => [' UX ', 'ux', '', 'Dados'],
        'github_url' => 'github.com/ana?tab=repositories',
        'linkedin_url' => 'br.linkedin.com/in/ana-silva/',
    ]))->assertOk()
        ->assertJsonPath('display_name', 'Ana   Maria  Silva')
        ->assertJsonPath('initials', 'AS')
        ->assertJsonPath('about', "Linha 1\nLinha 2")
        ->assertJsonPath('interests', ['UX', 'Dados'])
        ->assertJsonPath('github_url', 'https://github.com/ana')
        ->assertJsonPath('linkedin_url', 'https://www.linkedin.com/in/ana-silva')
        ->assertJsonPath('disciplines.0.name', 'Algoritmos I')
        ->assertJsonPath('complete', true);

    $this->actingAs($user)->getJson('/api/v1/me')->assertJsonPath('profile_complete', true);
});

it('salvar duas vezes não duplica disciplinas', function () {
    $user = User::factory()->create();
    $ids = Discipline::orderBy('id')->limit(2)->pluck('id')->all();
    $payload = profilePayload(['discipline_ids' => [...$ids, $ids[0]]]);

    $this->actingAs($user)->putJson('/api/v1/profile', $payload)->assertOk();
    $this->actingAs($user)->putJson('/api/v1/profile', $payload)->assertOk();

    expect($user->disciplines()->count())->toBe(2);
});

it('limpa links e textos opcionais vazios', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->putJson('/api/v1/profile', profilePayload([
        'about' => 'Algo', 'github_url' => 'github.com/ana', 'linkedin_url' => 'linkedin.com/in/ana',
    ]))->assertOk();

    $this->actingAs($user)->putJson('/api/v1/profile', profilePayload([
        'about' => '  ', 'github_url' => '', 'linkedin_url' => '',
    ]))->assertOk()
        ->assertJsonPath('about', null)
        ->assertJsonPath('github_url', null)
        ->assertJsonPath('linkedin_url', null);
});

it('aceita o corpo sem as listas de tags', function () {
    $payload = profilePayload();
    unset($payload['interests'], $payload['can_help_with'], $payload['needs_help_with']);

    $this->actingAs(User::factory()->create())->putJson('/api/v1/profile', $payload)
        ->assertOk()->assertJsonPath('needs_help_with', []);
});

it('valida com as mensagens da spec', function (array $override, string $field, string $message) {
    $this->actingAs(User::factory()->create())->putJson('/api/v1/profile', profilePayload($override))
        ->assertUnprocessable()
        ->assertJsonPath('errors', fn (array $errors) => $errors[$field] === [$message]); // o campo pode ter ponto, como discipline_ids.0
})->with([
    'nome vazio' => [['display_name' => '   '], 'display_name', 'Informe seu nome de exibição.'],
    'nome como lista' => [['display_name' => ['Ana']], 'display_name', 'Informe seu nome de exibição.'],
    'nome longo' => [['display_name' => str_repeat('a', 121)], 'display_name', 'Use no máximo 120 caracteres.'],
    'sem disciplina' => [['discipline_ids' => []], 'discipline_ids', 'Selecione pelo menos uma disciplina atual para completar seu perfil.'],
    'disciplina inexistente' => [['discipline_ids' => [999999]], 'discipline_ids.0', 'Selecione disciplinas da lista.'],
    'disciplina não numérica' => [['discipline_ids' => ['x']], 'discipline_ids.0', 'Selecione disciplinas da lista.'],
    'disciplinas como texto' => [['discipline_ids' => 'abc'], 'discipline_ids', 'Selecione pelo menos uma disciplina atual para completar seu perfil.'],
    'sobre mim longo' => [['about' => str_repeat('a', 5001)], 'about', 'Use no máximo 5.000 caracteres.'],
    'sobre mim como array' => [['about' => ['x']], 'about', 'Use no máximo 5.000 caracteres.'],
    'tag longa' => [['interests' => [str_repeat('a', 256)]], 'interests.0', 'Use no máximo 255 caracteres por assunto.'],
    'tags demais' => [['can_help_with' => array_map(fn ($i) => "t$i", range(1, 51))], 'can_help_with', 'Adicione no máximo 50 assuntos.'],
    'tags como texto' => [['needs_help_with' => 'Git'], 'needs_help_with', 'Adicione no máximo 50 assuntos.'],
    'tag não-texto' => [['interests' => [['x']]], 'interests.0', 'Use no máximo 255 caracteres por assunto.'],
    'github de repositório' => [['github_url' => 'https://github.com/ana/repo'], 'github_url', 'Informe o endereço do seu perfil no GitHub, como https://github.com/usuario.'],
    'github como array' => [['github_url' => ['x']], 'github_url', 'Informe o endereço do seu perfil no GitHub, como https://github.com/usuario.'],
    'linkedin de empresa' => [['linkedin_url' => 'https://www.linkedin.com/company/ifsul'], 'linkedin_url', 'Informe o endereço do seu perfil no LinkedIn, como https://www.linkedin.com/in/usuario.'],
]);

it('exige nome e disciplinas mesmo quando não vêm no corpo', function () {
    $this->actingAs(User::factory()->create())->putJson('/api/v1/profile', [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.display_name', ['Informe seu nome de exibição.'])
        ->assertJsonPath('errors.discipline_ids', ['Selecione pelo menos uma disciplina atual para completar seu perfil.']);
});

it('não grava nada quando a validação falha', function () {
    $user = User::factory()->create(['display_name' => 'Ana Silva']);
    $user->disciplines()->attach(Discipline::where('name', 'Algoritmos I')->value('id'));

    $this->actingAs($user)->putJson('/api/v1/profile', profilePayload([
        'display_name' => 'Outro Nome',
        'discipline_ids' => [Discipline::where('name', 'Compiladores')->value('id')],
        'github_url' => 'https://github.com/ana/repo',
    ]))->assertUnprocessable();

    expect($user->fresh()->display_name)->toBe('Ana Silva')
        ->and($user->disciplines()->pluck('name')->all())->toBe(['Algoritmos I']);
});

it('exige login', function () {
    $this->getJson('/api/v1/profile')->assertUnauthorized();
    $this->putJson('/api/v1/profile', profilePayload())->assertUnauthorized();
});
