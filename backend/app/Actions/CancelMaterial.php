<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;

final class CancelMaterial
{
    /** Devolve false quando o envio já não está pendente. */
    public function handle(Material $material): bool
    {
        return Material::whereKey($material->id)
            ->where('status', MaterialStatus::Pendente)
            ->update(['status' => MaterialStatus::Cancelado]) === 1;
    }
}
