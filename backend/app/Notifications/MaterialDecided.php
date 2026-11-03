<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Notifications\Notification;

// uma classe para os quatro eventos (aprovado/rejeitado, material/alteração): só o kind muda
final class MaterialDecided extends Notification
{
    public const KINDS = ['material_approved', 'material_rejected', 'revision_approved', 'revision_rejected'];

    /** @param  Material  $original  o material publicado (numa alteração, o original), para o link de Meus conteúdos */
    public function __construct(private readonly string $kind, private readonly Material $original) {}

    public static function for(Material $material, bool $approved): self
    {
        $isRevision = $material->revises_id !== null;
        $kind = ($isRevision ? 'revision_' : 'material_').($approved ? 'approved' : 'rejected');

        // numa alteração, o link e o código são os do material original
        return new self($kind, $isRevision ? Material::withTrashed()->findOrFail($material->revises_id) : $material);
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{kind: string, material_id: int, code: string, discipline_name: string, discipline_slug: string} */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'material_id' => $this->original->id,
            'code' => $this->original->code,
            'discipline_name' => $this->original->discipline->name,
            'discipline_slug' => $this->original->discipline->slug,
        ];
    }
}
