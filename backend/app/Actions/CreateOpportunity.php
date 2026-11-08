<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Http\Requests\StoreOpportunityRequest;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateOpportunity
{
    /** @param  array{type: string, title: string, description: string, url: string, modality: string, deadline: ?string}  $data */
    public function handle(User $author, array $data): Opportunity
    {
        $opportunity = new Opportunity($data);
        $opportunity->user_id = $author->id;
        $opportunity->status = MaterialStatus::Pendente;

        try {
            DB::transaction(function () use ($opportunity, $author): void {
                $opportunity->save();
                app(RecordAuditEvent::class)->handle(AuditAction::OpportunitySubmitted, $author, opportunity: $opportunity);
            });
        } catch (UniqueConstraintViolationException) {
            // dois envios do mesmo link ao mesmo tempo: o índice parcial barra o segundo
            throw ValidationException::withMessages(['url' => StoreOpportunityRequest::DUPLICATE_URL]);
        }

        return $opportunity;
    }
}
