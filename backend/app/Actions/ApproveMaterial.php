<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\User;
use App\Notifications\MaterialDecided;
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

                if (! $approved) {
                    return false;
                }

                // alteração: a versão publicada recebe os dados novos; a data pública do original não muda
                if ($material->revises_id !== null) {
                    Material::whereKey($material->revises_id)->update([
                        'title' => $material->title,
                        'description' => $material->description,
                        'url' => $material->url,
                    ]);
                }

                // na mesma transação: se a decisão não valer, a notificação também não existe
                $material->author->notify(MaterialDecided::for($material, approved: true));

                return true;
            });
        } catch (UniqueConstraintViolationException) {
            Conflict::abort('link_taken', 'O link desta alteração já foi compartilhado nesta disciplina.');
        }
    }
}
