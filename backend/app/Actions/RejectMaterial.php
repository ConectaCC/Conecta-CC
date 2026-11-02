<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\User;
use App\Notifications\MaterialDecided;
use Illuminate\Support\Facades\DB;

final class RejectMaterial
{
    /** Devolve false quando o material já não está pendente (outra decisão chegou antes). */
    public function handle(Material $material, User $moderator, string $reason): bool
    {
        return DB::transaction(function () use ($material, $moderator, $reason): bool {
            $rejected = Material::whereKey($material->id)
                ->where('status', MaterialStatus::Pendente)
                ->update([
                    'status' => MaterialStatus::Rejeitado,
                    'rejection_reason' => $reason,
                    'decided_by' => $moderator->id,
                    'decided_at' => now(),
                ]) === 1;

            if ($rejected) {
                $material->author->notify(MaterialDecided::for($material, approved: false));
            }

            return $rejected;
        });
    }
}
