<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;

/** @mixin DatabaseNotification */
final class NotificationResource extends JsonResource
{
    private const TEXTS = [
        'material_approved' => ['Seu material foi aprovado', 'O material enviado para %s foi aprovado.'],
        'material_rejected' => ['Seu material não foi aprovado', 'O material enviado para %s foi analisado.'],
        'revision_approved' => ['Sua alteração foi aprovada', 'A alteração do material em %s foi aprovada.'],
        'revision_rejected' => ['Sua alteração não foi aprovada', 'A alteração enviada para %s foi analisada.'],
    ];

    /** @return array{id: string, title: string, detail: string, ok: bool, read: bool, link: string, created_at: string} */
    public function toArray(Request $request): array
    {
        /** @var array{kind: string, code: string, discipline_name: string, discipline_slug: string} $data */
        $data = $this->data;
        [$title, $detail] = self::TEXTS[$data['kind']] ?? self::TEXTS['material_approved'];
        $approved = str_ends_with($data['kind'], 'approved');

        return [
            'id' => (string) $this->id,
            'title' => $title,
            'detail' => sprintf($detail, $data['discipline_name']),
            'ok' => $approved,
            'read' => $this->read_at !== null,
            // aprovado leva à disciplina; rejeitado leva ao item em Meus conteúdos (onde está o motivo)
            'link' => $approved ? '/estudos/'.$data['discipline_slug'] : '/perfil/meus-conteudos?item='.$data['code'],
            'created_at' => $this->created_at?->toIso8601String() ?? '',
        ];
    }
}
