<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

final class DeleteMaterial
{
    public function handle(Material $material): void
    {
        abort_unless($material->status === MaterialStatus::Aprovado, 409, 'Este material não pode ser excluído.');

        DB::transaction(function () use ($material): void {
            $material->revisions()->where('status', MaterialStatus::Pendente)->update(['status' => MaterialStatus::Cancelado]);
            $material->delete();
        });
    }
}
