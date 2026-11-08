<?php

use App\Models\Discipline;
use App\Models\User;
use App\Support\DisciplineCatalog;

it('carrega as 49 disciplinas obrigatórias da Matriz 2023', function () {
    expect(Discipline::count())->toBe(49)
        ->and(Discipline::where('semester', 8)->pluck('name')->all())
        ->toEqualCanonicalizing(['Gestão Empresarial e Empreendedorismo', 'Trabalho de Conclusão II'])
        ->and(Discipline::where('name', 'like', '%Empreededorismo%')->exists())->toBeFalse()
        ->and(Discipline::where('name', 'Computação Quântica')->exists())->toBeFalse() // eletiva
        ->and(Discipline::where('name', 'Algoritmos I')->sole()->syllabus_url)
        ->toBe('https://inf.passofundo.ifsul.edu.br/assets/docs/bcc/ementas/primeiro_semestre/PF_CC_1_Algoritmos_I.pdf');
});

it('não duplica ao sincronizar de novo', function () {
    DisciplineCatalog::sync(resource_path('catalog/matriz-2023.json'));

    expect(Discipline::count())->toBe(49);
});

it('sincronizar de novo restaura semestre, carga horária e ementa, e atualiza a data', function () {
    $algoritmos = Discipline::where('name', 'Algoritmos I')->sole();
    $algoritmos->forceFill(['semester' => 8, 'workload_hours' => 1, 'syllabus_url' => null])->save();
    $this->travel(1)->day();

    DisciplineCatalog::sync(resource_path('catalog/matriz-2023.json'));

    $synced = $algoritmos->fresh();
    expect($synced->semester)->toBe(1)
        ->and($synced->workload_hours)->toBe(75)
        ->and($synced->syllabus_url)->toEndWith('PF_CC_1_Algoritmos_I.pdf')
        ->and($synced->created_at)->not->toBeNull()
        ->and($synced->updated_at->isToday())->toBeTrue()
        ->and($synced->created_at->lt($synced->updated_at))->toBeTrue();
});

it('lista o catálogo ordenado por semestre e nome', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/disciplines')
        ->assertOk()
        ->assertJsonCount(49)
        ->assertJsonPath('0', [
            'id' => Discipline::where('name', 'Algoritmos I')->value('id'),
            'name' => 'Algoritmos I',
            'slug' => 'algoritmos-i',
            'semester' => 1,
            'workload_hours' => 75,
            'syllabus_url' => 'https://inf.passofundo.ifsul.edu.br/assets/docs/bcc/ementas/primeiro_semestre/PF_CC_1_Algoritmos_I.pdf',
        ]);
});

it('gera um slug único e legível para cada disciplina', function () {
    $slugs = Discipline::all()->pluck('slug');

    expect($slugs->unique())->toHaveCount(49)
        ->and(Discipline::where('name', 'Criação de Páginas WEB')->sole()->slug)->toBe('criacao-de-paginas-web')
        ->and($slugs->every(fn (?string $slug) => preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', (string) $slug) === 1))->toBeTrue();
});

it('exige login para ver o catálogo', function () {
    $this->getJson('/api/v1/disciplines')->assertUnauthorized();
});
