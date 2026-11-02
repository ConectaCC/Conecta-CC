<?php

use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use App\Notifications\MaterialDecided;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

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

// API

function notifyMany(User $user, int $total, int $unread): void
{
    $material = Material::factory()->for($user, 'author')->approved()->create();
    foreach (range(1, $total) as $i) {
        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => MaterialDecided::class,
            'data' => ['kind' => 'material_approved', 'material_id' => $material->id, 'code' => $material->code, 'discipline_name' => $material->discipline->name, 'discipline_slug' => $material->discipline->slug],
            'read_at' => $i <= $unread ? null : now(),
        ]);
        $notification->forceFill(['created_at' => now()->subMinutes($total - $i)])->save();
    }
}

it('lista as 20 mais recentes e conta todas as não lidas', function () {
    $user = User::factory()->create();
    notifyMany($user, 25, 12);
    notifyMany(User::factory()->create(), 3, 3);

    $response = $this->actingAs($user)->getJson('/api/v1/notifications')->assertOk()
        ->assertJsonCount(20, 'data')
        ->assertJsonPath('unread_count', 12);
    $dates = array_column($response->json('data'), 'created_at');
    expect($dates)->toBe(collect($dates)->sortDesc()->values()->all());
});

it('monta título, detalhe e link de cada tipo', function (string $kind, string $title, string $detail, string $link) {
    $user = User::factory()->create();
    $material = Material::factory()->for($user, 'author')->for(Discipline::where('name', 'Algoritmos I')->sole())->approved()->create();
    $user->notify(new MaterialDecided($kind, $material));

    $this->actingAs($user)->getJson('/api/v1/notifications')
        ->assertJsonPath('data.0.title', $title)
        ->assertJsonPath('data.0.detail', $detail)
        ->assertJsonPath('data.0.link', str_replace('{code}', $material->code, $link))
        ->assertJsonPath('data.0.ok', str_ends_with($kind, 'approved'))
        ->assertJsonPath('data.0.read', false);
})->with([
    ['material_approved', 'Seu material foi aprovado', 'O material enviado para Algoritmos I foi aprovado.', '/estudos/algoritmos-i'],
    ['material_rejected', 'Seu material não foi aprovado', 'O material enviado para Algoritmos I foi analisado.', '/perfil/meus-conteudos?item={code}'],
    ['revision_approved', 'Sua alteração foi aprovada', 'A alteração do material em Algoritmos I foi aprovada.', '/estudos/algoritmos-i'],
    ['revision_rejected', 'Sua alteração não foi aprovada', 'A alteração enviada para Algoritmos I foi analisada.', '/perfil/meus-conteudos?item={code}'],
]);

it('marca uma como lida', function () {
    $user = User::factory()->create();
    notifyMany($user, 2, 2);
    $id = $user->notifications()->latest()->first()->id;

    $this->actingAs($user)->postJson("/api/v1/notifications/{$id}/read")->assertNoContent();

    $this->actingAs($user)->getJson('/api/v1/notifications')->assertJsonPath('unread_count', 1);
});

it('não deixa marcar a notificação de outro usuário', function () {
    $owner = User::factory()->create();
    notifyMany($owner, 1, 1);
    $id = $owner->notifications()->sole()->id;

    $this->actingAs(User::factory()->create())->postJson("/api/v1/notifications/{$id}/read")->assertNotFound();

    expect($owner->notifications()->sole()->read_at)->toBeNull();
});

it('marca todas como lidas só para o usuário', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    notifyMany($user, 5, 5);
    notifyMany($other, 2, 2);

    $this->actingAs($user)->postJson('/api/v1/notifications/read-all')->assertNoContent();

    expect($user->unreadNotifications()->count())->toBe(0)->and($other->unreadNotifications()->count())->toBe(2);
});

it('exige login nas rotas de notificação', function () {
    $this->getJson('/api/v1/notifications')->assertUnauthorized();
    $this->postJson('/api/v1/notifications/qualquer/read')->assertUnauthorized();
    $this->postJson('/api/v1/notifications/read-all')->assertUnauthorized();
});
