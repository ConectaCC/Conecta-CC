<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Support\Conflict;
use Illuminate\Support\Facades\DB;

final class DeleteMaterial
{
    public function handle(Material $material): void
    {
        DB::transaction(function () use ($material): void {
            // mesma trava da edição: as duas ações sobre o mesmo material acontecem uma depois da outra
            $locked = Material::whereKey($material->id)->lockForUpdate()->first();

            if ($locked === null || $locked->status !== MaterialStatus::Aprovado) {
                Conflict::abort('not_deletable', 'Este material não pode ser excluído.');
            }

            $locked->revisions()->where('status', MaterialStatus::Pendente)->update(['status' => MaterialStatus::Cancelado]);
            $locked->delete();
        });
    }
}
