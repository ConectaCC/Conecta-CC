<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Opportunity;
use App\Support\Conflict;
use Illuminate\Support\Facades\DB;

final class DeleteOpportunity
{
    public function handle(Opportunity $opportunity): void
    {
        DB::transaction(function () use ($opportunity): void {
            $locked = Opportunity::whereKey($opportunity->id)->lockForUpdate()->first();

            if ($locked === null || $locked->status !== MaterialStatus::Aprovado) {
                Conflict::abort('not_deletable', 'Esta oportunidade não pode ser excluída.');
            }

            $locked->delete();
            app(RecordAuditEvent::class)->handle(AuditAction::OpportunityDeleted, $locked->author, opportunity: $locked);
        });
    }
}
