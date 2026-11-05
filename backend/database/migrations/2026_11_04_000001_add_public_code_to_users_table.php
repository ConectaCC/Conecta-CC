<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // código aleatório da URL do Perfil público: o banco gera para os usuários existentes e para os novos
        DB::statement("ALTER TABLE users ADD COLUMN public_code varchar(16) NOT NULL DEFAULT left(replace(gen_random_uuid()::text, '-', ''), 16)");
        DB::statement('CREATE UNIQUE INDEX users_public_code_unique ON users (public_code)');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('public_code');
        });
    }
};
