<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\OpportunityDecided;
use Illuminate\Support\Facades\DB;

final class RejectOpportunity
{
    /** Devolve false quando a oportunidade já não está pendente. */
    public function handle(Opportunity $opportunity, User $moderator, string $reason): bool
    {
        return DB::transaction(function () use ($opportunity, $moderator, $reason): bool {
            $rejected = Opportunity::whereKey($opportunity->id)
                ->where('status', MaterialStatus::Pendente)
                ->update(['status' => MaterialStatus::Rejeitado, 'rejection_reason' => $reason, 'decided_by' => $moderator->id, 'decided_at' => now()]) === 1;

            if ($rejected) {
                $opportunity->author->notify(OpportunityDecided::for($opportunity, approved: false));
                app(RecordAuditEvent::class)->handle(AuditAction::OpportunityRejected, $moderator, opportunity: $opportunity);
            }

            return $rejected;
        });
    }
}
