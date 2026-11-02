<?php

use App\Actions\CreateMaterial;
use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

function hardeningDiscipline(): Discipline
{
    return Discipline::where('name', 'Algoritmos I')->sole();
}

it('garante no banco uma só alteração pendente por material', function () {
    $original = Material::factory()->approved()->create();
    Material::factory()->for($original->author, 'author')->for($original->discipline)->create(['revises_id' => $original->id]);

    expect(fn () => Material::factory()->for($original->author, 'author')->for($original->discipline)->create(['revises_id' => $original->id]))
        ->toThrow(UniqueConstraintViolationException::class);
});

it('recusa edição sem nenhuma mudança', function () {
    $material = Material::factory()->approved()->create(['title' => 'Igual', 'description' => null, 'url' => 'https://site.com/igual']);

    $this->actingAs($material->author)
        ->putJson("/api/v1/materials/{$material->id}", ['title' => '  Igual ', 'description' => '', 'url' => 'site.com/igual'])
        ->assertConflict()
        ->assertJsonPath('message', 'Nenhuma alteração foi feita no material.');
    expect(Material::where('revises_id', $material->id)->exists())->toBeFalse();
});

it('identifica o tipo de conflito com um código', function () {
    $moderator = User::factory()->moderator()->create();
    $cancelled = Material::factory()->create(['status' => MaterialStatus::Cancelado->value]);
    $decided = Material::factory()->approved()->create();

    $this->actingAs($moderator)->postJson("/api/v1/moderation/materials/{$cancelled->id}/approve")->assertConflict()->assertJsonPath('code', 'material_cancelled');
    $this->actingAs($moderator)->postJson("/api/v1/moderation/materials/{$decided->id}/approve")->assertConflict()->assertJsonPath('code', 'already_decided');

    $original = Material::factory()->approved()->create(['url' => 'https://site.com/antigo']);
    $this->actingAs($original->author)->putJson("/api/v1/materials/{$original->id}", ['title' => 'Novo', 'url' => 'https://site.com/disputado'])->assertCreated();
    Material::factory()->for($original->discipline)->create(['url' => 'https://site.com/disputado']);
    $revision = Material::where('revises_id', $original->id)->sole();

    $this->actingAs($moderator)->postJson("/api/v1/moderation/materials/{$revision->id}/approve")->assertConflict()->assertJsonPath('code', 'link_taken');
});

it('exige login para cancelar, editar e excluir', function () {
    $material = Material::factory()->approved()->create();

    $this->postJson("/api/v1/materials/{$material->id}/cancel")->assertUnauthorized();
    $this->putJson("/api/v1/materials/{$material->id}", ['title' => 'x', 'url' => 'https://site.com/x'])->assertUnauthorized();
    $this->deleteJson("/api/v1/materials/{$material->id}")->assertUnauthorized();
});

it('não deixa editar nem excluir uma alteração diretamente', function () {
    $original = Material::factory()->approved()->create();
    $revision = Material::factory()->for($original->author, 'author')->for($original->discipline)->approved()->create(['revises_id' => $original->id]);

    $this->actingAs($original->author)->putJson("/api/v1/materials/{$revision->id}", ['title' => 'x', 'url' => 'https://site.com/x'])->assertForbidden();
    $this->actingAs($original->author)->deleteJson("/api/v1/materials/{$revision->id}")->assertForbidden();
});

it('transforma a corrida de link repetido em erro de validação', function () {
    $user = User::factory()->create();
    Material::factory()->for(hardeningDiscipline())->create(['url' => 'https://site.com/corrida']);

    expect(fn () => app(CreateMaterial::class)->handle($user, hardeningDiscipline(), ['title' => 'Corrida', 'description' => null, 'url' => 'https://site.com/corrida', 'anonymous' => false]))
        ->toThrow(ValidationException::class);
});

it('conta no limite só os envios que deram certo', function () {
    $user = User::factory()->create();
    $url = '/api/v1/disciplines/'.hardeningDiscipline()->id.'/materials';

    foreach (range(1, 12) as $i) {
        $this->actingAs($user)->postJson($url, ['title' => '', 'url' => "https://site.com/erro-{$i}"])->assertUnprocessable();
    }

    $this->actingAs($user)->postJson($url, ['title' => 'Vale', 'url' => 'https://site.com/vale'])->assertCreated();
});

it('remove bytes nulos do texto em vez de dar erro', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/disciplines/'.hardeningDiscipline()->id.'/materials', [
        'title' => "Lista\u{0000} de exercícios",
        'description' => "Com\u{0000} gabarito.",
        'url' => 'https://site.com/nul',
    ])->assertCreated();
    expect(Material::where('url', 'https://site.com/nul')->sole())->title->toBe('Lista de exercícios')->description->toBe('Com gabarito.');

    $this->actingAs($user)->putJson('/api/v1/profile', [
        'display_name' => "Ana\u{0000} Silva",
        'discipline_ids' => [hardeningDiscipline()->id],
        'about' => null, 'interests' => [], 'can_help_with' => [], 'needs_help_with' => [], 'github_url' => null, 'linkedin_url' => null,
    ])->assertOk()->assertJsonPath('display_name', 'Ana Silva');
});
