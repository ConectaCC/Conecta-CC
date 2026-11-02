<?php

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

function materialRow(array $overrides = []): array
{
    return Material::factory()->raw($overrides);
}

it('recusa status fora da lista', function () {
    expect(fn () => DB::table('materials')->insert([...materialRow(), 'status' => 'X']))->toThrow(QueryException::class);
});

it('exige motivo só quando rejeitado', function () {
    expect(fn () => DB::table('materials')->insert([...materialRow(), 'status' => 'REJEITADO', 'rejection_reason' => null]))
        ->toThrow(QueryException::class)
        ->and(fn () => DB::table('materials')->insert([...materialRow(), 'status' => 'APROVADO', 'rejection_reason' => 'motivo']))
        ->toThrow(QueryException::class);
});

it('recusa o mesmo link ativo duas vezes na mesma disciplina', function (MaterialStatus $first, MaterialStatus $second) {
    $discipline = Discipline::first();
    Material::factory()->for($discipline)->create(['url' => 'https://site.com/x', 'status' => $first]);

    expect(fn () => Material::factory()->for($discipline)->create(['url' => 'https://site.com/x', 'status' => $second]))
        ->toThrow(UniqueConstraintViolationException::class);
})->with([
    'pendente e pendente' => [MaterialStatus::Pendente, MaterialStatus::Pendente],
    'aprovado e pendente' => [MaterialStatus::Aprovado, MaterialStatus::Pendente],
]);

it('aceita o mesmo link quando o anterior não está ativo ou é de outra disciplina', function () {
    [$one, $two] = Discipline::take(2)->get();
    Material::factory()->for($one)->rejected('Link quebrado.')->create(['url' => 'https://site.com/a']);
    Material::factory()->for($one)->create(['url' => 'https://site.com/b', 'status' => MaterialStatus::Cancelado]);
    Material::factory()->for($one)->approved()->create(['url' => 'https://site.com/c'])->delete();
    Material::factory()->for($one)->create(['url' => 'https://site.com/d']);

    foreach (['a', 'b', 'c'] as $path) {
        Material::factory()->for($one)->create(['url' => "https://site.com/{$path}"]);
    }
    Material::factory()->for($two)->create(['url' => 'https://site.com/d']);

    expect(Material::withTrashed()->count())->toBe(8);
});

it('gera o código da solicitação', function () {
    expect(Material::factory()->create()->code)->toMatch('/^MAT-\d{5}$/');
});
