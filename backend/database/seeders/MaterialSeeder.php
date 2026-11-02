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
            ['Algoritmos I', 'aluno@academico.ifsul.edu.br', 'Lista de exercícios de recursão', null, 'https://example.com/recursao', false, MaterialStatus::Rejeitado],
            ['Cálculo Diferencial e Integral I', 'aluno@academico.ifsul.edu.br', 'Resumo de limites', 'Resumo com exemplos.', 'https://example.com/limites', false, MaterialStatus::Cancelado],
            ['Circuitos Digitais', 'aluno@academico.ifsul.edu.br', 'Mapas de Karnaugh', 'Passo a passo.', 'https://example.com/karnaugh', false, MaterialStatus::Aprovado],
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
            if ($status === MaterialStatus::Rejeitado) {
                $material->rejection_reason = 'Link pede login no Moodle.';
                $material->decided_at = now();
            }
            $material->save();
        }

        // alteração pendente de um material aprovado (Meus conteúdos e Moderação da etapa 2b)
        $karnaugh = Material::where('url', 'https://example.com/karnaugh')->firstOrFail();
        if (! $karnaugh->revisions()->exists()) {
            $revision = new Material(['title' => 'Mapas de Karnaugh com exercícios', 'description' => 'Passo a passo e lista resolvida.', 'url' => 'https://example.com/karnaugh-v2', 'anonymous' => $karnaugh->anonymous]);
            $revision->discipline_id = $karnaugh->discipline_id;
            $revision->user_id = $karnaugh->user_id;
            $revision->revises_id = $karnaugh->id;
            $revision->status = MaterialStatus::Pendente;
            $revision->save();
        }
    }
}
