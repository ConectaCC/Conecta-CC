<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;

final class CancelOpportunity
{
    /** Devolve false quando o envio já não está pendente. */
    public function handle(Opportunity $opportunity): bool
    {
        return DB::transaction(function () use ($opportunity): bool {
            $cancelled = Opportunity::whereKey($opportunity->id)
                ->where('status', MaterialStatus::Pendente)
                ->update(['status' => MaterialStatus::Cancelado]) === 1;

            if ($cancelled) {
                // só quem enviou cancela (OpportunityPolicy::manage)
                app(RecordAuditEvent::class)->handle(AuditAction::OpportunityCancelled, $opportunity->author, opportunity: $opportunity);
            }

            return $cancelled;
        });
    }
}
