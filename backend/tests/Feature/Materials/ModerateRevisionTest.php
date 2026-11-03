<?php

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\User;

function reviseVia($test, Material $original, array $overrides = []): Material
{
    $test->actingAs($original->author)
        ->putJson("/api/v1/materials/{$original->id}", array_merge(['title' => 'Título novo', 'description' => 'Nova.', 'url' => 'https://site.com/novo'], $overrides))
        ->assertCreated();

    return Material::where('revises_id', $original->id)->where('status', MaterialStatus::Pendente)->sole();
}

it('mostra na fila a alteração com a versão publicada', function () {
    $original = Material::factory()->approved()->create(['title' => 'Título antigo', 'url' => 'https://site.com/antigo']);
    $revision = reviseVia($this, $original);

    $this->actingAs(User::factory()->administrator()->create())->getJson('/api/v1/moderation/materials')
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $revision->id)
        ->assertJsonPath('0.title', 'Título novo')
        ->assertJsonPath('0.revision_of.title', 'Título antigo')
        ->assertJsonPath('0.revision_of.url', 'https://site.com/antigo');
});

it('o envio original na fila não tem revision_of', function () {
    Material::factory()->create();

    $this->actingAs(User::factory()->administrator()->create())->getJson('/api/v1/moderation/materials')
        ->assertJsonPath('0.revision_of', null);
});

it('aprovar a alteração atualiza o original e mantém a data pública', function () {
    $original = Material::factory()->approved()->create(['title' => 'Título antigo', 'published_at' => now()->subWeek()]);
    $publishedAt = $original->published_at->toIso8601String();
    $revision = reviseVia($this, $original);

    $this->actingAs(User::factory()->moderator()->create())->postJson("/api/v1/moderation/materials/{$revision->id}/approve")->assertOk();

    expect($original->refresh())
        ->title->toBe('Título novo')
        ->description->toBe('Nova.')
        ->url->toBe('https://site.com/novo')
        ->and($original->published_at->toIso8601String())->toBe($publishedAt)
        ->and($revision->refresh()->status)->toBe(MaterialStatus::Aprovado);
    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$original->discipline_id}/materials")
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $original->id)
        ->assertJsonPath('data.0.title', 'Título novo');
});

it('rejeitar a alteração mantém o original', function () {
    $original = Material::factory()->approved()->create(['title' => 'Título antigo']);
    $revision = reviseVia($this, $original);

    $this->actingAs(User::factory()->moderator()->create())
        ->postJson("/api/v1/moderation/materials/{$revision->id}/reject", ['reason' => 'Fora do tema.'])
        ->assertOk();

    expect($original->refresh()->title)->toBe('Título antigo')
        ->and($revision->refresh())->status->toBe(MaterialStatus::Rejeitado)->rejection_reason->toBe('Fora do tema.');
});

it('recusa aprovar a alteração cujo link passou a repetir outro envio', function () {
    $original = Material::factory()->approved()->create(['title' => 'Título antigo', 'url' => 'https://site.com/antigo']);
    $revision = reviseVia($this, $original, ['url' => 'https://site.com/disputado']);
    Material::factory()->for($original->discipline)->create(['url' => 'https://site.com/disputado']);

    $this->actingAs(User::factory()->administrator()->create())->postJson("/api/v1/moderation/materials/{$revision->id}/approve")
        ->assertConflict()
        ->assertJsonPath('message', 'O link desta alteração já foi compartilhado nesta disciplina.');

    expect($revision->refresh()->status)->toBe(MaterialStatus::Pendente)
        ->and($original->refresh())->title->toBe('Título antigo')->url->toBe('https://site.com/antigo');
});

it('mantém o anonimato depois de aprovar a alteração', function () {
    $original = Material::factory()->approved()->anonymous()->create();
    $revision = reviseVia($this, $original, ['anonymous' => false]);

    $this->actingAs(User::factory()->administrator()->create())->postJson("/api/v1/moderation/materials/{$revision->id}/approve")->assertOk();

    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$original->discipline_id}/materials")
        ->assertJsonPath('data.0.author_name', null);
});

it('avisa que o envio foi cancelado pelo autor', function (string $action) {
    $material = Material::factory()->create();
    $this->actingAs($material->author)->postJson("/api/v1/materials/{$material->id}/cancel")->assertOk();

    $this->actingAs(User::factory()->moderator()->create())
        ->postJson("/api/v1/moderation/materials/{$material->id}/{$action}", ['reason' => 'Motivo.'])
        ->assertConflict()
        ->assertJsonPath('message', 'Este conteúdo foi cancelado pelo autor.');
})->with(['approve', 'reject']);

it('esconde do MODERADOR a alteração do próprio material', function () {
    $moderator = User::factory()->moderator()->create();
    $original = Material::factory()->for($moderator, 'author')->approved()->create();
    reviseVia($this, $original);

    $this->actingAs($moderator)->getJson('/api/v1/moderation/materials')->assertJsonCount(0);
});
