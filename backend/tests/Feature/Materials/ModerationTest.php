<?php

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\User;

const QUEUE = '/api/v1/moderation/materials';

function approveUrl(Material $material): string
{
    return QUEUE."/{$material->id}/approve";
}

function rejectUrl(Material $material): string
{
    return QUEUE."/{$material->id}/reject";
}

it('nega a Moderação ao ALUNO e a quem não está logado', function () {
    $material = Material::factory()->create();
    $student = User::factory()->create();

    $this->actingAs($student)->getJson(QUEUE)->assertForbidden();
    $this->actingAs($student)->postJson(approveUrl($material))->assertForbidden();
    $this->actingAs($student)->postJson(rejectUrl($material), ['reason' => 'Não.'])->assertForbidden();
    auth()->forgetGuards();
    $this->getJson(QUEUE)->assertUnauthorized();
});

it('lista só os pendentes, do mais antigo para o mais novo', function () {
    $newer = Material::factory()->create(['created_at' => now()->subHour()]);
    $older = Material::factory()->create(['created_at' => now()->subDays(2)]);
    Material::factory()->approved()->create();
    Material::factory()->rejected('Não.')->create();
    Material::factory()->create()->delete();

    $this->actingAs(User::factory()->moderator()->create())->getJson(QUEUE)
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonPath('0.id', $older->id)
        ->assertJsonPath('1.id', $newer->id)
        ->assertJsonPath('0.discipline.slug', $older->discipline->slug);
});

it('esconde do MODERADOR o próprio envio e mostra ao ADMINISTRADOR o dele', function () {
    $moderator = User::factory()->moderator()->create();
    $admin = User::factory()->administrator()->create();
    Material::factory()->for($moderator, 'author')->create();
    $adminOwn = Material::factory()->for($admin, 'author')->create();

    $this->actingAs($moderator)->getJson(QUEUE)->assertJsonCount(1)->assertJsonPath('0.id', $adminOwn->id);
    $this->actingAs($admin)->getJson(QUEUE)->assertJsonCount(2)
        ->assertJsonPath('1.is_own', true)
        ->assertJsonPath('0.is_own', false);
});

it('não envia ao MODERADOR o nome do autor anônimo', function () {
    $author = User::factory()->create(['display_name' => 'Carla Anônima']);
    Material::factory()->for($author, 'author')->anonymous()->create();

    $this->actingAs(User::factory()->moderator()->create())->getJson(QUEUE)
        ->assertJsonPath('0.author_name', null)
        ->assertJsonPath('0.anonymous', true)
        ->assertDontSee('Carla Anônima')
        ->assertDontSee($author->email);

    $this->actingAs(User::factory()->administrator()->create())->getJson(QUEUE)
        ->assertJsonPath('0.author_name', 'Carla Anônima')
        ->assertJsonPath('0.anonymous', true);
});

it('mostra o nome do autor identificado aos dois papéis', function () {
    Material::factory()->for(User::factory()->create(['display_name' => 'Ana Visível']), 'author')->create();

    $this->actingAs(User::factory()->moderator()->create())->getJson(QUEUE)->assertJsonPath('0.author_name', 'Ana Visível');
});

it('aprova e publica o material', function () {
    $moderator = User::factory()->moderator()->create();
    $material = Material::factory()->create();

    $this->actingAs($moderator)->postJson(approveUrl($material))->assertOk();

    $material->refresh();
    expect($material->status)->toBe(MaterialStatus::Aprovado)
        ->and($material->published_at)->not->toBeNull()
        ->and($material->decided_at)->not->toBeNull()
        ->and($material->decided_by)->toBe($moderator->id);
    $this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$material->discipline_id}/materials")
        ->assertJsonPath('data.0.id', $material->id);
});

it('exige motivo para rejeitar', function (array $payload, string $message) {
    $material = Material::factory()->create();

    $this->actingAs(User::factory()->administrator()->create())->postJson(rejectUrl($material), $payload)
        ->assertUnprocessable()
        ->assertJsonPath('errors.reason.0', $message);
    expect($material->refresh()->status)->toBe(MaterialStatus::Pendente);
})->with([
    'sem motivo' => [[], 'Informe o motivo da rejeição.'],
    'só espaços' => [['reason' => '   '], 'Informe o motivo da rejeição.'],
    'motivo como array' => [['reason' => ['x']], 'Informe o motivo da rejeição.'],
    'longo demais' => [['reason' => str_repeat('a', 501)], 'Use no máximo 500 caracteres.'],
]);

it('rejeita com motivo', function () {
    $material = Material::factory()->create();
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->postJson(rejectUrl($material), ['reason' => '  O link pede login.  '])
        ->assertOk();

    expect($material->refresh())
        ->status->toBe(MaterialStatus::Rejeitado)
        ->rejection_reason->toBe('O link pede login.')
        ->decided_by->toBe($moderator->id)
        ->decided_at->not->toBeNull()
        ->published_at->toBeNull();
});

it('não deixa a segunda decisão sobrescrever a primeira', function () {
    $material = Material::factory()->create();

    $this->actingAs(User::factory()->moderator()->create())->postJson(approveUrl($material))->assertOk();
    $this->actingAs(User::factory()->administrator()->create())
        ->postJson(rejectUrl($material), ['reason' => 'Tarde demais.'])
        ->assertConflict()
        ->assertJsonPath('message', 'Este conteúdo já foi analisado.');
    $this->actingAs(User::factory()->moderator()->create())->postJson(approveUrl($material))->assertConflict();

    expect($material->refresh())->status->toBe(MaterialStatus::Aprovado)->rejection_reason->toBeNull();
});

it('impede o MODERADOR de decidir sobre o próprio envio e deixa o ADMINISTRADOR', function () {
    $moderator = User::factory()->moderator()->create();
    $admin = User::factory()->administrator()->create();
    $moderatorOwn = Material::factory()->for($moderator, 'author')->create();
    $adminOwn = Material::factory()->for($admin, 'author')->create();

    $this->actingAs($moderator)->postJson(approveUrl($moderatorOwn))->assertForbidden();
    $this->actingAs($moderator)->postJson(rejectUrl($moderatorOwn), ['reason' => 'x'])->assertForbidden();
    $this->actingAs($admin)->postJson(approveUrl($adminOwn))->assertOk();
});

it('trata material excluído como inexistente', function () {
    $material = Material::factory()->create();
    $material->delete();

    $this->actingAs(User::factory()->administrator()->create())->postJson(approveUrl($material))->assertNotFound();
});
