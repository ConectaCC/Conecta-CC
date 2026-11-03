<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;
use UnexpectedValueException;

/** @mixin DatabaseNotification */
final class NotificationResource extends JsonResource
{
    // título, detalhe e se foi aprovado saem da mesma entrada: nada fica contraditório
    private const TEXTS = [
        'material_approved' => ['Seu material foi aprovado', 'O material enviado para %s foi aprovado.', true],
        'material_rejected' => ['Seu material não foi aprovado', 'O material enviado para %s foi analisado.', false],
        'revision_approved' => ['Sua alteração foi aprovada', 'A alteração do material em %s foi aprovada.', true],
        'revision_rejected' => ['Sua alteração não foi aprovada', 'A alteração enviada para %s foi analisada.', false],
    ];

    /** @return array{id: string, title: string, detail: string, ok: bool, read: bool, link: string, created_at: string} */
    public function toArray(Request $request): array
    {
        /** @var array{kind: string, code: string, discipline_name: string, discipline_slug: string} $data */
        $data = $this->data;
        // só MaterialDecided grava notificações; um tipo desconhecido é erro de programação, não dado do usuário
        [$title, $detail, $approved] = self::TEXTS[$data['kind']] ?? throw new UnexpectedValueException("Tipo de notificação desconhecido: {$data['kind']}");

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
