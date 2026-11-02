<?php

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;

function editPayload(array $overrides = []): array
{
    return array_merge(['title' => 'Título novo', 'description' => 'Descrição nova.', 'url' => 'https://site.com/novo'], $overrides);
}

function others(): array
{
    return [User::factory()->create(), User::factory()->moderator()->create(), User::factory()->administrator()->create()];
}

// cancelar

it('deixa o autor cancelar o envio pendente', function () {
    $material = Material::factory()->create();

    $this->actingAs($material->author)->postJson("/api/v1/materials/{$material->id}/cancel")
        ->assertOk()
        ->assertJsonPath('status', 'CANCELADO');

    expect($material->refresh()->status)->toBe(MaterialStatus::Cancelado);
    $this->actingAs(User::factory()->administrator()->create())->getJson('/api/v1/moderation/materials')->assertJsonCount(0);
});

it('não cancela o que não está pendente', function (string $state) {
    $material = Material::factory()->{$state}(...($state === 'rejected' ? ['Não.'] : []))->create();

    $this->actingAs($material->author)->postJson("/api/v1/materials/{$material->id}/cancel")
        ->assertConflict()
        ->assertJsonPath('message', 'Este envio não pode ser cancelado.');
})->with(['approved', 'rejected']);

it('só o autor cancela', function () {
    $material = Material::factory()->create();

    foreach (others() as $other) {
        $this->actingAs($other)->postJson("/api/v1/materials/{$material->id}/cancel")->assertForbidden();
    }
    expect($material->refresh()->status)->toBe(MaterialStatus::Pendente);
});

// editar

it('cria uma alteração pendente e mantém a versão pública', function () {
    $material = Material::factory()->approved()->create(['title' => 'Título antigo']);

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload())
        ->assertCreated()
        ->assertJsonPath('pending_revision.title', 'Título novo');

    $revision = Material::where('revises_id', $material->id)->sole();
    expect($revision->status)->toBe(MaterialStatus::Pendente)
        ->and($revision->url)->toBe('https://site.com/novo')
        ->and($revision->discipline_id)->toBe($material->discipline_id)
        ->and($revision->user_id)->toBe($material->user_id);
    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$material->discipline_id}/materials")
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Título antigo');
});

it('aceita o mesmo link do próprio material e recusa o de outro', function () {
    $material = Material::factory()->approved()->create(['url' => 'https://site.com/meu']);
    Material::factory()->for($material->discipline)->create(['url' => 'https://site.com/outro']);

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload(['url' => 'site.com/meu']))->assertCreated();
    Material::where('revises_id', $material->id)->delete();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload(['url' => 'https://site.com/outro']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.url.0', 'Esse link já foi compartilhado nesta disciplina.');
});

it('não deixa duas alterações pendentes', function () {
    $material = Material::factory()->approved()->create();
    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload())->assertCreated();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload(['url' => 'https://site.com/outra']))
        ->assertConflict()
        ->assertJsonPath('message', 'Já existe uma alteração deste material em análise.');
});

it('só edita material aprovado', function (string $state) {
    $material = Material::factory()->{$state}(...($state === 'rejected' ? ['Não.'] : []))->create();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload())
        ->assertConflict()
        ->assertJsonPath('message', 'Este material não pode ser editado.');
})->with(['rejected', 'anonymous']);

it('não edita material excluído', function () {
    $material = Material::factory()->approved()->create();
    $material->delete();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload())->assertNotFound();
});

it('mantém o anonimato na alteração', function () {
    $material = Material::factory()->approved()->anonymous()->create();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload(['anonymous' => false]))->assertCreated();

    expect(Material::where('revises_id', $material->id)->sole()->anonymous)->toBeTrue();
});

it('valida a edição como o envio', function () {
    $material = Material::factory()->approved()->create();

    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload(['title' => '  ']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.title.0', 'Informe um título para o material.');
});

it('só o autor edita', function () {
    $material = Material::factory()->approved()->create();

    foreach (others() as $other) {
        $this->actingAs($other)->putJson("/api/v1/materials/{$material->id}", editPayload())->assertForbidden();
        $this->actingAs($other)->putJson("/api/v1/materials/{$material->id}", editPayload(['title' => '']))->assertForbidden();
    }
    expect(Material::where('revises_id', $material->id)->exists())->toBeFalse();
});

it('conta a edição no limite de envios', function () {
    $author = User::factory()->create();
    $discipline = Discipline::where('name', 'Algoritmos I')->sole();
    foreach (range(1, 10) as $i) {
        $this->actingAs($author)->postJson("/api/v1/disciplines/{$discipline->id}/materials", editPayload(['url' => "https://site.com/{$i}"]))->assertCreated();
    }
    $material = Material::factory()->for($author, 'author')->approved()->create();

    $this->actingAs($author)->putJson("/api/v1/materials/{$material->id}", editPayload())->assertTooManyRequests();
});

// excluir

it('exclui o material aprovado e cancela a alteração pendente', function () {
    $material = Material::factory()->approved()->create();
    $this->actingAs($material->author)->putJson("/api/v1/materials/{$material->id}", editPayload())->assertCreated();

    $this->actingAs($material->author)->deleteJson("/api/v1/materials/{$material->id}")->assertNoContent();

    expect($material->refresh()->trashed())->toBeTrue()
        ->and(Material::where('revises_id', $material->id)->sole()->status)->toBe(MaterialStatus::Cancelado);
    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$material->discipline_id}/materials")->assertJsonCount(0, 'data');
    $this->actingAs(User::factory()->administrator()->create())->getJson('/api/v1/moderation/materials')->assertJsonCount(0);
    $this->actingAs($material->author)->getJson('/api/v1/me/materials')->assertJsonPath('data.0.status', 'EXCLUIDO');
});

it('não exclui o que não está aprovado', function () {
    $material = Material::factory()->create();

    $this->actingAs($material->author)->deleteJson("/api/v1/materials/{$material->id}")
        ->assertConflict()
        ->assertJsonPath('message', 'Este material não pode ser excluído.');
});

it('só o autor exclui, e excluir de novo dá 404', function () {
    $material = Material::factory()->approved()->create();

    foreach (others() as $other) {
        $this->actingAs($other)->deleteJson("/api/v1/materials/{$material->id}")->assertForbidden();
    }
    $this->actingAs($material->author)->deleteJson("/api/v1/materials/{$material->id}")->assertNoContent();
    $this->actingAs($material->author)->deleteJson("/api/v1/materials/{$material->id}")->assertNotFound();
});

it('não deixa agir sobre uma alteração diretamente', function () {
    $material = Material::factory()->approved()->create();
    $revision = Material::factory()->for($material->author, 'author')->for($material->discipline)->create(['revises_id' => $material->id]);

    $this->actingAs($material->author)->postJson("/api/v1/materials/{$revision->id}/cancel")->assertForbidden();
});
