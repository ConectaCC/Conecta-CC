<?php

namespace App\Actions;

use App\Enums\MaterialStatus;
use App\Models\Material;

final class ReviseMaterial
{
    /**
     * Cria a alteração pendente; a versão publicada só muda quando ela for aprovada.
     *
     * @param  array{title: string, description: ?string, url: string, anonymous: bool}  $data
     */
    public function handle(Material $original, array $data): Material
    {
        abort_unless($original->status === MaterialStatus::Aprovado, 409, 'Este material não pode ser editado.');
        abort_if(
            $original->revisions()->where('status', MaterialStatus::Pendente)->exists(),
            409,
            'Já existe uma alteração deste material em análise.',
        );

        $revision = new Material([
            'title' => $data['title'],
            'description' => $data['description'],
            'url' => $data['url'],
            // a condição de autoria é escolhida no envio e não muda na edição (decisoes.md 55.2)
            'anonymous' => $original->anonymous,
        ]);
        $revision->discipline_id = $original->discipline_id;
        $revision->user_id = $original->user_id;
        $revision->revises_id = $original->id;
        $revision->status = MaterialStatus::Pendente;
        $revision->save();

        return $revision;
    }
}
