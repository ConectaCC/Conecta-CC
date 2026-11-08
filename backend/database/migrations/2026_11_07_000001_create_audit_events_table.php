<?php

use App\Enums\AuditAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->string('action', 40);
            // nulo só quando quem fez foi o workflow "Promover usuário"
            $table->foreignId('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->restrictOnDelete();
            $table->jsonb('data')->default('{}');
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index(['action', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });

        $actions = collect(AuditAction::cases())->map(fn (AuditAction $action) => "'{$action->value}'")->implode(',');
        DB::statement("ALTER TABLE audit_events ADD CONSTRAINT audit_events_action_check CHECK (action IN ({$actions}))");
        DB::statement("ALTER TABLE audit_events ADD CONSTRAINT audit_events_actor_check CHECK (actor_id IS NOT NULL OR action = 'role_changed')");

        // o histórico não muda depois de gravado: nem o painel nem um comando manual alteram ou apagam eventos
        // OR REPLACE: o migrate:fresh apaga as tabelas, mas não as funções
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION audit_events_immutable() RETURNS trigger LANGUAGE plpgsql AS $$
            BEGIN
                RAISE EXCEPTION 'audit_events não pode ser alterada nem apagada';
            END;
            $$;
            CREATE TRIGGER audit_events_no_change BEFORE UPDATE OR DELETE ON audit_events
                FOR EACH ROW EXECUTE FUNCTION audit_events_immutable();
            CREATE TRIGGER audit_events_no_truncate BEFORE TRUNCATE ON audit_events
                FOR EACH STATEMENT EXECUTE FUNCTION audit_events_immutable();
            SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        DB::unprepared('DROP FUNCTION IF EXISTS audit_events_immutable()');
    }
};
