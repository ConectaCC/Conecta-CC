<?php

namespace Database\Seeders;

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;

// só dev; pode rodar sozinho: php artisan db:seed --class=MaterialSeeder
class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $user = fn (string $email) => User::where('email', $email)->firstOrFail();
        $discipline = fn (string $name) => Discipline::where('name', $name)->firstOrFail();

        $materials = [
            ['Estrutura de Dados III', 'aluno@academico.ifsul.edu.br', 'Revisão de Árvores AVL', 'Resumo e exercícios para revisão.', 'https://example.com/revisao-arvores-avl', false, MaterialStatus::Aprovado],
            ['Estrutura de Dados III', 'aluno@academico.ifsul.edu.br', 'Lista de exercícios sobre árvores B', 'Exercícios comentados de inserção e remoção.', 'https://example.com/lista-arvores-b', true, MaterialStatus::Aprovado],
            ['Estrutura de Dados III', 'moderador2@academico.ifsul.edu.br', 'Visualizador de estruturas de dados', null, 'https://example.com/visualizador', false, MaterialStatus::Aprovado],
            ['Algoritmos I', 'aluno@academico.ifsul.edu.br', 'Exercícios de lógica com Python', 'Lista com gabarito.', 'https://example.com/logica-python', false, MaterialStatus::Pendente],
            ['Sistemas Distribuídos I', 'moderador@academico.ifsul.edu.br', 'Resumo de relógios lógicos', null, 'https://example.com/relogios-logicos', false, MaterialStatus::Pendente],
        ];

        foreach ($materials as [$disciplineName, $email, $title, $description, $url, $anonymous, $status]) {
            $material = Material::firstOrNew(['discipline_id' => $discipline($disciplineName)->id, 'url' => $url]);
            if ($material->exists) {
                continue;
            }
            $material->fill(['title' => $title, 'description' => $description, 'url' => $url, 'anonymous' => $anonymous]);
            $material->user_id = $user($email)->id;
            $material->status = $status;
            if ($status === MaterialStatus::Aprovado) {
                $material->published_at = $material->decided_at = now();
            }
            $material->save();
        }
    }
}
