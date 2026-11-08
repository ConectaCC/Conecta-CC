<?php

use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Filament\Resources\Materials\Pages\ListMaterials;
use App\Models\Material;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->create(['role' => Role::Administrador]);
    $this->actingAs($this->admin, 'web');
});

function historyMaterial(MaterialStatus $status = MaterialStatus::Pendente, array $attributes = []): Material
{
    return Material::factory()->create([
        'status' => $status->value,
        'rejection_reason' => $status === MaterialStatus::Rejeitado ? 'Link fora do tema.' : null,
        ...$attributes,
    ]);
}

it('mostra a situação de cada material, e excluído vale mais que o status', function (MaterialStatus $status, bool $trashed, string $label) {
    $material = historyMaterial($status);
    if ($trashed) {
        $material->delete();
    }

    Livewire::test(ListMaterials::class)
        ->assertCanSeeTableRecords([$material])
        ->assertTableColumnStateSet('situation', [$label], $material);
})->with([
    [MaterialStatus::Pendente, false, 'Pendente'],
    [MaterialStatus::Aprovado, false, 'Aprovado'],
    [MaterialStatus::Rejeitado, false, 'Rejeitado'],
    [MaterialStatus::Cancelado, false, 'Cancelado'],
    [MaterialStatus::Aprovado, true, 'Excluído'],
]);

it('marca a alteração sem esconder a situação', function () {
    $original = historyMaterial(MaterialStatus::Aprovado);
    $revision = historyMaterial(attributes: ['revises_id' => $original->id]);

    Livewire::test(ListMaterials::class)
        ->assertCanSeeTableRecords([$original, $revision])
        ->assertTableColumnStateSet('situation', ['Pendente', 'Alteração'], $revision)
        ->assertTableColumnStateSet('situation', ['Aprovado'], $original);
});

it('mostra o autor real do material anônimo com a marca', function () {
    $author = User::factory()->create(['display_name' => 'Autora Sigilosa']);
    historyMaterial(attributes: ['user_id' => $author->id, 'anonymous' => true]);

    Livewire::test(ListMaterials::class)->assertSee('Autora Sigilosa')->assertSee('Enviado anonimamente');
});

it('não marca como anônimo o material identificado', function () {
    historyMaterial();

    Livewire::test(ListMaterials::class)->assertDontSee('Enviado anonimamente');
});

it('mostra quem decidiu e usa traço quando ninguém decidiu', function () {
    $moderator = User::factory()->create(['role' => Role::Moderador, 'display_name' => 'Marcos Moderador']);
    $decided = historyMaterial(MaterialStatus::Aprovado, ['decided_by' => $moderator->id, 'decided_at' => now()]);
    $pending = historyMaterial();

    Livewire::test(ListMaterials::class)
        ->assertCanSeeTableRecords([$decided, $pending])
        ->assertSee('Marcos Moderador')
        ->assertSee('—');
});

it('mostra o código e a disciplina junto com o título', function () {
    $material = historyMaterial(attributes: ['title' => 'Resumo de Cálculo']);

    Livewire::test(ListMaterials::class)
        ->assertSee('Resumo de Cálculo')
        ->assertSee($material->code.' · '.$material->discipline->name);
});

it('mostra as datas no horário de Brasília', function () {
    $this->travelTo('2026-11-08 02:30:00'); // UTC: ainda é dia 07 em Brasília
    historyMaterial(MaterialStatus::Aprovado, ['decided_by' => $this->admin->id, 'decided_at' => now()]);

    Livewire::test(ListMaterials::class)->assertSee('07/11/2026')->assertDontSee('08/11/2026');
});

it('filtra pela situação', function (string $filter) {
    $pending = historyMaterial();
    $approved = historyMaterial(MaterialStatus::Aprovado);
    $deleted = historyMaterial(MaterialStatus::Aprovado);
    $deleted->delete();
    $expected = ['PENDENTE' => $pending, 'APROVADO' => $approved, 'EXCLUIDO' => $deleted][$filter];

    Livewire::test(ListMaterials::class)
        ->filterTable('situation', $filter)
        ->assertCanSeeTableRecords([$expected])
        ->assertCanNotSeeTableRecords(collect([$pending, $approved, $deleted])->reject(fn ($material) => $material->is($expected))->all());
})->with(['PENDENTE', 'APROVADO', 'EXCLUIDO']);

it('busca por título e por autor', function (string $search) {
    $author = User::factory()->create(['display_name' => 'Zuleika Quintanilha']);
    $found = historyMaterial(attributes: ['user_id' => $author->id, 'title' => 'Resumo de Cálculo']);
    $other = historyMaterial(attributes: ['title' => 'Lista de Física']);

    Livewire::test(ListMaterials::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords([$found])
        ->assertCanNotSeeTableRecords([$other]);
})->with(['Resumo', 'Zuleika']);

it('mostra no detalhe a descrição, o link, o motivo e os dados originais', function () {
    $original = historyMaterial(MaterialStatus::Aprovado, ['title' => 'Título original', 'url' => 'https://example.com/original']);
    $revision = historyMaterial(MaterialStatus::Rejeitado, [
        'revises_id' => $original->id,
        'description' => 'Descrição enviada',
        'url' => 'https://example.com/novo',
    ]);
    $original->delete(); // o original excluído continua aparecendo no detalhe

    Livewire::test(ListMaterials::class)
        ->mountAction(TestAction::make('view')->table($revision))
        ->assertMountedActionModalSee(['Descrição enviada', 'https://example.com/novo', 'Link fora do tema.', 'Título original', 'https://example.com/original', 'Enviado em', 'Decidido por', 'Decidido em']);
});

it('não oferece nenhuma ação que altere dados', function () {
    $material = historyMaterial();
    $before = $material->fresh()->toArray();

    $page = Livewire::test(ListMaterials::class)->assertActionDoesNotExist('create');
    foreach (['edit', 'delete', 'forceDelete', 'restore', 'approve', 'reject'] as $action) {
        $page->assertActionDoesNotExist(TestAction::make($action)->table($material));
    }
    $page->mountAction(TestAction::make('view')->table($material));

    expect($material->fresh()->toArray())->toBe($before);
});

it('fica fechado para quem não é ADMINISTRADOR', function (Role $role) {
    $this->actingAs(User::factory()->create(['role' => $role]), 'web')->get('/admin/moderacoes')->assertForbidden();
})->with([Role::Aluno, Role::Moderador]);

it('abre a página para o ADMINISTRADOR', function () {
    historyMaterial();

    $this->get('/admin/moderacoes')->assertOk()->assertSee('Moderações');
});
