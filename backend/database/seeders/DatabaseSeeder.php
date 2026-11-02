<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            return; // contas de teste nunca vão para produção
        }

        $accounts = [
            ['Ana Aluna', 'aluno@academico.ifsul.edu.br', Role::Aluno],
            ['Marcos Moderador', 'moderador@academico.ifsul.edu.br', Role::Moderador],
            ['Mariana Moderadora', 'moderador2@academico.ifsul.edu.br', Role::Moderador],
            ['Alice Administradora', 'admin@academico.ifsul.edu.br', Role::Administrador],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            User::factory()->create([
                'name' => $name,
                'display_name' => $name,
                'email' => $email,
                'password' => 'password',
                'role' => $role,
            ]);
        }

        $this->call(MaterialSeeder::class);
    }
}
