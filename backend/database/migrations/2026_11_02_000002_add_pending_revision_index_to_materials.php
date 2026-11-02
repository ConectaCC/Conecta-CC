<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // uma só alteração pendente por material, garantido também no banco (spec 4.2)
        DB::statement("CREATE UNIQUE INDEX materials_one_pending_revision ON materials (revises_id) WHERE status = 'PENDENTE' AND revises_id IS NOT NULL");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS materials_one_pending_revision');
    }
};
