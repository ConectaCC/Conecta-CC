<?php

// o formato exato de cada item que a SPA consome: um campo a mais, a menos ou trocado quebra a tela

use App\Models\Discipline;
use App\Models\Material;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\MaterialDecided;

beforeEach(function () {
    $this->travelTo('2026-11-08 12:00:00');
    $this->algoritmos = Discipline::where('name', 'Algoritmos I')->sole();
    $this->author = User::factory()->create(['display_name' => 'Beatriz Souza'])->refresh(); // o código público vem do banco
});

function opportunityOf(User $author, array $attributes = []): Opportunity
{
    return Opportunity::factory()->for($author, 'author')->create([
        'type' => 'ESTAGIO',
        'title' => 'Estágio em web',
        'description' => 'Vaga para o 3º semestre.',
        'url' => 'https://empresa.com/vaga',
        'modality' => 'REMOTO',
        'deadline' => '2026-12-01',
        ...$attributes,
    ])->refresh();
}

function materialOf(User $author, Discipline $discipline, array $attributes = []): Material
{
    return Material::factory()->for($author, 'author')->for($discipline)->create([
        'title' => 'Lista 1',
        'description' => 'Com gabarito.',
        'url' => 'https://site.com/lista',
        ...$attributes,
    ])->refresh();
}

it('item da fila de oportunidades', function () {
    $opportunity = opportunityOf($this->author);

    expect($this->actingAs(User::factory()->moderator()->create())->getJson('/api/v1/moderation/opportunities')->json('0'))->toBe([
        'id' => $opportunity->id,
        'code' => $opportunity->code,
        'type' => 'ESTAGIO',
        'title' => 'Estágio em web',
        'description' => 'Vaga para o 3º semestre.',
        'url' => 'https://empresa.com/vaga',
        'modality' => 'REMOTO',
        'deadline' => '2026-12-01',
        'author_name' => 'Beatriz Souza',
        'is_own' => false,
        'submitted_at' => '2026-11-08T12:00:00+00:00',
    ]);
});

it('item de Minhas oportunidades', function () {
    $opportunity = opportunityOf($this->author);

    expect($this->actingAs($this->author)->getJson('/api/v1/me/opportunities')->json('data.0'))->toBe([
        'id' => $opportunity->id,
        'code' => $opportunity->code,
        'type' => 'ESTAGIO',
        'title' => 'Estágio em web',
        'description' => 'Vaga para o 3º semestre.',
        'url' => 'https://empresa.com/vaga',
        'modality' => 'REMOTO',
        'deadline' => '2026-12-01',
        'status' => 'PENDENTE',
        'rejection_reason' => null,
        'submitted_at' => '2026-11-08T12:00:00+00:00',
    ]);
});

it('item da fila de materiais, com a versão no ar da alteração', function () {
    $original = materialOf($this->author, $this->algoritmos, ['status' => 'APROVADO', 'published_at' => now()]);
    $revision = materialOf($this->author, $this->algoritmos, ['revises_id' => $original->id, 'title' => 'Lista 1 revisada', 'url' => 'https://site.com/lista-2']);

    expect($this->actingAs(User::factory()->moderator()->create())->getJson('/api/v1/moderation/materials')->json('0'))->toBe([
        'id' => $revision->id,
        'code' => $revision->code,
        'title' => 'Lista 1 revisada',
        'description' => 'Com gabarito.',
        'url' => 'https://site.com/lista-2',
        'anonymous' => false,
        'author_name' => 'Beatriz Souza',
        'is_own' => false,
        'submitted_at' => '2026-11-08T12:00:00+00:00',
        'discipline' => ['id' => $this->algoritmos->id, 'name' => 'Algoritmos I', 'slug' => 'algoritmos-i'],
        'revision_of' => ['title' => 'Lista 1', 'description' => 'Com gabarito.', 'url' => 'https://site.com/lista'],
    ]);
});

it('item dos materiais da disciplina', function () {
    $material = materialOf($this->author, $this->algoritmos, ['status' => 'APROVADO', 'published_at' => now()]);

    expect($this->actingAs(User::factory()->create())->getJson("/api/v1/disciplines/{$this->algoritmos->id}/materials")->json('data.0'))->toBe([
        'id' => $material->id,
        'code' => $material->code,
        'title' => 'Lista 1',
        'description' => 'Com gabarito.',
        'url' => 'https://site.com/lista',
        'author_name' => 'Beatriz Souza',
        'author_code' => $this->author->public_code,
        'published_at' => '2026-11-08T12:00:00+00:00',
        'is_mine' => false,
    ]);
});

it('item de Meus materiais, com a alteração rejeitada', function () {
    $material = materialOf($this->author, $this->algoritmos, ['status' => 'APROVADO', 'published_at' => now(), 'anonymous' => true]);
    materialOf($this->author, $this->algoritmos, ['revises_id' => $material->id, 'status' => 'REJEITADO', 'rejection_reason' => 'Link quebrado.', 'decided_at' => now()]);

    expect($this->actingAs($this->author)->getJson('/api/v1/me/materials')->json('data.0'))->toBe([
        'id' => $material->id,
        'code' => $material->code,
        'title' => 'Lista 1',
        'description' => 'Com gabarito.',
        'url' => 'https://site.com/lista',
        'anonymous' => true,
        'status' => 'APROVADO',
        'rejection_reason' => null,
        'submitted_at' => '2026-11-08T12:00:00+00:00',
        'discipline' => ['id' => $this->algoritmos->id, 'name' => 'Algoritmos I', 'slug' => 'algoritmos-i'],
        'pending_revision' => null,
        'rejected_revision' => ['reason' => 'Link quebrado.', 'decided_at' => '2026-11-08T12:00:00+00:00'],
    ]);
});

it('alteração pendente dentro do item de Meus materiais', function () {
    $material = materialOf($this->author, $this->algoritmos, ['status' => 'APROVADO', 'published_at' => now()]);
    materialOf($this->author, $this->algoritmos, ['revises_id' => $material->id, 'title' => 'Lista 1 revisada', 'description' => null, 'url' => 'https://site.com/lista-2']);

    expect($this->actingAs($this->author)->getJson('/api/v1/me/materials')->json('data.0.pending_revision'))->toBe([
        'title' => 'Lista 1 revisada',
        'description' => null,
        'url' => 'https://site.com/lista-2',
        'submitted_at' => '2026-11-08T12:00:00+00:00',
    ]);
});

it('item das notificações', function () {
    $material = materialOf($this->author, $this->algoritmos, ['status' => 'APROVADO', 'published_at' => now()]);
    $this->author->notify(new MaterialDecided('material_approved', $material));

    expect($this->actingAs($this->author)->getJson('/api/v1/notifications')->json('data.0'))->toBe([
        'id' => $this->author->notifications()->sole()->id,
        'title' => 'Seu material foi aprovado',
        'detail' => 'O material enviado para Algoritmos I foi aprovado.',
        'ok' => true,
        'read' => false,
        'link' => '/estudos/algoritmos-i',
        'created_at' => '2026-11-08T12:00:00+00:00',
    ]);
});

it('listas de assuntos do próprio Perfil e do perfil público', function () {
    $this->author->update(['interests' => ['Dados'], 'can_help_with' => ['Python'], 'needs_help_with' => ['Cálculo']]);

    foreach (['/api/v1/profile', '/api/v1/students/'.$this->author->public_code] as $url) {
        $this->actingAs($this->author)->getJson($url)
            ->assertJsonPath('interests', ['Dados'])
            ->assertJsonPath('can_help_with', ['Python'])
            ->assertJsonPath('needs_help_with', ['Cálculo']);
    }
});
