<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\User;
use App\Support\Conflict;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

final class ApproveMaterial
{
    /** Devolve false quando o material já não está pendente (outra decisão ou cancelamento chegou antes). */
    public function handle(Material $material, User $moderator): bool
    {
        $now = now();

        try {
            return DB::transaction(function () use ($material, $moderator, $now): bool {
                $approved = Material::whereKey($material->id)
                    ->where('status', MaterialStatus::Pendente)
                    ->update([
                        'status' => MaterialStatus::Aprovado,
                        'published_at' => $now,
                        'decided_by' => $moderator->id,
                        'decided_at' => $now,
                    ]) === 1;

                // alteração: a versão publicada recebe os dados novos; a data pública do original não muda
                if ($approved && $material->revises_id !== null) {
                    Material::whereKey($material->revises_id)->update([
                        'title' => $material->title,
                        'description' => $material->description,
                        'url' => $material->url,
                    ]);
                }

                return $approved;
            });
        } catch (UniqueConstraintViolationException) {
            Conflict::abort('link_taken', 'O link desta alteração já foi compartilhado nesta disciplina.');
        }
    }
}
