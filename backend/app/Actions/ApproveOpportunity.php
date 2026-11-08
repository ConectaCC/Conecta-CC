<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\OpportunityDecided;
use Illuminate\Support\Facades\DB;

final class ApproveOpportunity
{
    /** Devolve false quando a oportunidade já não está pendente (outra decisão ou cancelamento chegou antes). */
    public function handle(Opportunity $opportunity, User $moderator): bool
    {
        return DB::transaction(function () use ($opportunity, $moderator): bool {
            $now = now();
            $approved = Opportunity::whereKey($opportunity->id)
                ->where('status', MaterialStatus::Pendente)
                ->update(['status' => MaterialStatus::Aprovado, 'published_at' => $now, 'decided_by' => $moderator->id, 'decided_at' => $now]) === 1;

            if ($approved) {
                // na mesma transação: se a decisão não valer, a notificação e o evento também não existem
                $opportunity->author->notify(OpportunityDecided::for($opportunity, approved: true));
                app(RecordAuditEvent::class)->handle(AuditAction::OpportunityApproved, $moderator, opportunity: $opportunity);
            }

            return $approved;
        });
    }
}
