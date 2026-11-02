<?php

use App\Models\Material;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

function approve($test, Material $material, ?User $moderator = null)
{
    return $test->actingAs($moderator ?? User::factory()->administrator()->create())->postJson("/api/v1/moderation/materials/{$material->id}/approve");
}

function reject($test, Material $material, ?User $moderator = null)
{
    return $test->actingAs($moderator ?? User::factory()->administrator()->create())->postJson("/api/v1/moderation/materials/{$material->id}/reject", ['reason' => 'Motivo.']);
}

function revisionOf(Material $original): Material
{
    return Material::factory()->for($original->author, 'author')->for($original->discipline)->create(['revises_id' => $original->id]);
}

// geração

it('notifica o autor uma vez por decisão, com o tipo certo', function (string $kind) {
    $original = Material::factory()->approved()->create();
    $target = str_starts_with($kind, 'revision') ? revisionOf($original) : Material::factory()->create();
    $author = $target->author;

    str_ends_with($kind, 'approved') ? approve($this, $target)->assertOk() : reject($this, $target)->assertOk();

    $notifications = $author->notifications()->get();
    expect($notifications)->toHaveCount(1);
    $expected = str_starts_with($kind, 'revision') ? $original : $target;
    expect($notifications->first()->data)->toMatchArray([
        'kind' => $kind,
        'material_id' => $expected->id,
        'code' => $expected->code,
        'discipline_slug' => $expected->discipline->slug,
        'discipline_name' => $expected->discipline->name,
    ]);
    expect(DatabaseNotification::count())->toBe(1);
})->with(['material_approved', 'material_rejected', 'revision_approved', 'revision_rejected']);

it('não notifica de novo quando a decisão chega tarde', function () {
    $material = Material::factory()->create();

    approve($this, $material)->assertOk();
    reject($this, $material)->assertConflict();
    approve($this, $material)->assertConflict();

    expect(DatabaseNotification::count())->toBe(1);
});

it('não notifica quando a aprovação da alteração esbarra em link repetido', function () {
    $original = Material::factory()->approved()->create(['url' => 'https://site.com/antigo']);
    $revision = revisionOf($original);
    $revision->update(['url' => 'https://site.com/disputado']);
    Material::factory()->for($original->discipline)->create(['url' => 'https://site.com/disputado']);

    approve($this, $revision)->assertConflict();

    expect(DatabaseNotification::count())->toBe(0);
});

it('não expõe nomes na notificação do material anônimo', function () {
    $author = User::factory()->create(['display_name' => 'Autora Escondida']);
    $moderator = User::factory()->moderator()->create(['display_name' => 'Moderador Fulano']);
    $material = Material::factory()->for($author, 'author')->anonymous()->create();

    approve($this, $material, $moderator)->assertOk();

    $json = json_encode($author->notifications()->sole()->data, JSON_UNESCAPED_UNICODE);
    expect($json)->not->toContain('Autora Escondida')->not->toContain('Moderador Fulano');
});
