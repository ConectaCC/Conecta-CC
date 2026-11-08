<?php

use App\Enums\AuditAction;
use App\Enums\OpportunityModality;
use App\Enums\OpportunityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            // quem enviou fica só no registro interno: nenhuma área pública mostra (decisoes.md §70)
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('type', 20);
            $table->string('title', 200);
            $table->string('description', 300);
            $table->string('url', 2048);
            $table->string('modality', 20);
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('PENDENTE');
            $table->string('rejection_reason', 500)->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['status', 'published_at']);
            $table->index(['user_id', 'created_at']);
        });

        $in = fn (array $cases) => collect($cases)->map(fn ($case) => "'{$case->value}'")->implode(',');
        DB::statement('ALTER TABLE opportunities ADD CONSTRAINT opportunities_type_check CHECK (type IN ('.$in(OpportunityType::cases()).'))');
        DB::statement('ALTER TABLE opportunities ADD CONSTRAINT opportunities_modality_check CHECK (modality IN ('.$in(OpportunityModality::cases()).'))');
        DB::statement("ALTER TABLE opportunities ADD CONSTRAINT opportunities_status_check CHECK (status IN ('PENDENTE','APROVADO','REJEITADO','CANCELADO'))");
        DB::statement("ALTER TABLE opportunities ADD CONSTRAINT opportunities_rejection_reason_check CHECK ((status = 'REJEITADO') = (rejection_reason IS NOT NULL))");
        // o mesmo link não fica ativo duas vezes; rejeitado, cancelado ou excluído libera
        DB::statement("CREATE UNIQUE INDEX opportunities_url_active ON opportunities (url) WHERE status IN ('PENDENTE','APROVADO') AND deleted_at IS NULL");

        // a auditoria passa a apontar também para a oportunidade (regra §66.7)
        Schema::table('audit_events', function (Blueprint $table) {
            $table->foreignId('opportunity_id')->nullable()->after('material_id')->constrained()->restrictOnDelete();
        });
        DB::statement('ALTER TABLE audit_events DROP CONSTRAINT audit_events_action_check');
        DB::statement('ALTER TABLE audit_events ADD CONSTRAINT audit_events_action_check CHECK (action IN ('.$in(AuditAction::cases()).'))');
    }

    public function down(): void
    {
        Schema::table('audit_events', fn (Blueprint $table) => $table->dropConstrainedForeignId('opportunity_id'));
        Schema::dropIfExists('opportunities');
    }
};
