<?php

use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

function codeOf(User $user): string
{
    return $user->refresh()->public_code;
}

it('mostra só os campos permitidos do perfil', function () {
    $student = User::factory()->moderator()->create([
        'name' => 'Nome Completo Secreto',
        'display_name' => 'Ana Silva',
        'email' => 'secreto@academico.ifsul.edu.br',
        'about' => 'Gosto de dados.',
        'interests' => ['Dados'],
        'can_help_with' => ['Python'],
        'needs_help_with' => ['Cálculo'],
        'github_url' => 'https://github.com/ana',
        'linkedin_url' => 'https://www.linkedin.com/in/ana',
    ]);
    $student->disciplines()->attach(Discipline::where('name', 'Algoritmos I')->value('id'));

    $response = $this->actingAs(User::factory()->create())->getJson('/api/v1/students/'.codeOf($student))->assertOk();

    expect(array_keys($response->json()))->toEqualCanonicalizing([
        'display_name', 'initials', 'about', 'interests', 'can_help_with', 'needs_help_with', 'github_url', 'linkedin_url', 'disciplines',
    ]);
    $response->assertJsonPath('display_name', 'Ana Silva')
        ->assertJsonPath('initials', 'AS')
        ->assertJsonPath('about', 'Gosto de dados.')
        ->assertJsonPath('interests', ['Dados'])
        ->assertJsonPath('disciplines.0.name', 'Algoritmos I')
        ->assertDontSee('Nome Completo Secreto')
        ->assertDontSee('secreto@academico.ifsul.edu.br')
        ->assertDontSee('MODERADOR');
});

it('não mostra histórico privado nem materiais da pessoa', function () {
    $student = User::factory()->create();
    Material::factory()->for($student, 'author')->rejected('Motivo privado.')->create(['title' => 'Título privado']);

    $this->actingAs(User::factory()->create())->getJson('/api/v1/students/'.codeOf($student))
        ->assertOk()
        ->assertDontSee('Título privado')
        ->assertDontSee('Motivo privado.');
});

it('responde 404 para código desconhecido, inválido ou id interno', function () {
    $student = User::factory()->create();
    $viewer = User::factory()->create();

    foreach (['0000000000000000', 'abc', (string) $student->id, strtoupper(codeOf($student))] as $code) {
        $this->actingAs($viewer)->getJson("/api/v1/students/{$code}")->assertNotFound();
    }
});

it('exige login', function () {
    $this->getJson('/api/v1/students/'.codeOf(User::factory()->create()))->assertUnauthorized();
});

it('gera códigos distintos, aleatórios e não sequenciais', function () {
    $codes = User::factory()->count(5)->create()->map(fn (User $user) => codeOf($user));

    expect($codes->unique())->toHaveCount(5)
        ->and($codes->every(fn (string $code) => preg_match('/^[0-9a-f]{16}$/', $code) === 1))->toBeTrue();
});

it('não deixa alterar o código pelo Perfil', function () {
    $user = User::factory()->create();
    $code = codeOf($user);

    $this->actingAs($user)->putJson('/api/v1/profile', [
        'display_name' => 'Ana', 'discipline_ids' => [Discipline::where('name', 'Algoritmos I')->value('id')], 'about' => null,
        'interests' => [], 'can_help_with' => [], 'needs_help_with' => [], 'github_url' => null, 'linkedin_url' => null,
        'public_code' => 'ffffffffffffffff',
    ])->assertOk();

    expect(codeOf($user))->toBe($code);
});

it('envia o código só do autor identificado', function () {
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    $visible = User::factory()->create();
    $hidden = User::factory()->create();
    Material::factory()->for($discipline)->for($visible, 'author')->approved()->create(['published_at' => now()]);
    Material::factory()->for($discipline)->for($hidden, 'author')->approved()->anonymous()->create(['published_at' => now()->subDay()]);

    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$discipline->id}/materials")
        ->assertJsonPath('data.0.author_code', codeOf($visible))
        ->assertJsonPath('data.1.author_code', null)
        ->assertDontSee(codeOf($hidden));
});

it('não envia o código do autor na Moderação', function () {
    $author = User::factory()->create();
    Material::factory()->for($author, 'author')->anonymous()->create();

    $this->actingAs(User::factory()->moderator()->create())->getJson('/api/v1/moderation/materials')
        ->assertOk()
        ->assertDontSee(codeOf($author));
});
