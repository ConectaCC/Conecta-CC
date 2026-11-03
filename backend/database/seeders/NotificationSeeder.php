<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use App\Notifications\MaterialDecided;
use Illuminate\Database\Seeder;

// só dev; pode rodar sozinho: php artisan db:seed --class=NotificationSeeder
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $aluno = User::where('email', 'aluno@academico.ifsul.edu.br')->firstOrFail();
        $examples = [
            'material_approved' => 'https://example.com/revisao-arvores-avl',
            'material_rejected' => 'https://example.com/recursao',
        ];

        foreach ($examples as $kind => $url) {
            $material = Material::withTrashed()->where('url', $url)->first();
            // a coluna data da tabela notifications é texto (não JSON): a comparação fica no PHP
            $exists = $aluno->notifications->contains(fn ($notification) => ($notification->data['kind'] ?? null) === $kind
                && ($notification->data['material_id'] ?? null) === $material?->id);
            if ($material !== null && ! $exists) {
                $aluno->notify(new MaterialDecided($kind, $material));
            }
        }
    }
}
