<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

final class CancelMaterial
{
    /** Devolve false quando o envio já não está pendente. */
    public function handle(Material $material): bool
    {
        return DB::transaction(function () use ($material): bool {
            $cancelled = Material::whereKey($material->id)
                ->where('status', MaterialStatus::Pendente)
                ->update(['status' => MaterialStatus::Cancelado]) === 1;

            if ($cancelled) {
                // só o autor cancela o próprio envio (MaterialPolicy::manage)
                app(RecordAuditEvent::class)->handle(AuditAction::SubmissionCancelled, $material->author, material: $material);
            }

            return $cancelled;
        });
    }
}
