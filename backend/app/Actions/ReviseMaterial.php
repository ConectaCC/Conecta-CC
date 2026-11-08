<?php

namespace App\Actions;

use App\Enums\AuditAction;
use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Support\Conflict;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

final class ReviseMaterial
{
    /**
     * Cria a alteração pendente; a versão publicada só muda quando ela for aprovada.
     *
     * @param  array{title: string, description: ?string, url: string, anonymous: bool}  $data
     */
    public function handle(Material $original, array $data): Material
    {
        try {
            return DB::transaction(function () use ($original, $data): Material {
                // trava o original: uma exclusão ao mesmo tempo não deixa alteração órfã
                $locked = Material::whereKey($original->id)->lockForUpdate()->first();

                if ($locked === null || $locked->status !== MaterialStatus::Aprovado) {
                    Conflict::abort('not_editable', 'Este material não pode ser editado.');
                }
                if ($data['title'] === $locked->title && $data['description'] === $locked->description && $data['url'] === $locked->url) {
                    Conflict::abort('no_changes', 'Nenhuma alteração foi feita no material.');
                }

                $revision = new Material([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'url' => $data['url'],
                    // a condição de autoria é escolhida no envio e não muda na edição (decisoes.md 55.2)
                    'anonymous' => $locked->anonymous,
                ]);
                $revision->discipline_id = $locked->discipline_id;
                $revision->user_id = $locked->user_id;
                $revision->revises_id = $locked->id;
                $revision->status = MaterialStatus::Pendente;
                $revision->save();
                app(RecordAuditEvent::class)->handle(AuditAction::RevisionSubmitted, $locked->author, material: $revision);

                return $revision;
            });
        } catch (UniqueConstraintViolationException) {
            // o índice único de alteração pendente por material é quem garante uma só, mesmo em envios simultâneos
            Conflict::abort('revision_pending', 'Já existe uma alteração deste material em análise.');
        }
    }
}
