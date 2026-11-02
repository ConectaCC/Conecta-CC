<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discipline_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            // alteração de material publicado (etapa 2b): aponta para o original
            $table->foreignId('revises_id')->nullable()->constrained('materials')->restrictOnDelete();
            $table->string('title', 200);
            $table->string('description', 2000)->nullable();
            $table->string('url', 2048);
            $table->boolean('anonymous')->default(false);
            $table->string('status', 20)->default('PENDENTE');
            $table->string('rejection_reason', 500)->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['discipline_id', 'status', 'published_at']);
        });

        DB::statement("ALTER TABLE materials ADD CONSTRAINT materials_status_check CHECK (status IN ('PENDENTE','APROVADO','REJEITADO','CANCELADO'))");
        DB::statement("ALTER TABLE materials ADD CONSTRAINT materials_rejection_reason_check CHECK ((status = 'REJEITADO') = (rejection_reason IS NOT NULL))");
        // link repetido na mesma disciplina (spec 4.1): só conta envio ativo e original, não alteração
        DB::statement("CREATE UNIQUE INDEX materials_discipline_url_active ON materials (discipline_id, url) WHERE status IN ('PENDENTE','APROVADO') AND deleted_at IS NULL AND revises_id IS NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
