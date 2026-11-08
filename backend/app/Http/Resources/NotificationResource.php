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
        // na oportunidade, o %s é o título: ela não tem disciplina
        'opportunity_approved' => ['Sua oportunidade foi aprovada', 'A oportunidade “%s” foi aprovada.', true],
        'opportunity_rejected' => ['Sua oportunidade não foi aprovada', 'A oportunidade “%s” foi analisada.', false],
    ];

    /** @return array{id: string, title: string, detail: string, ok: bool, read: bool, link: string, created_at: string} */
    public function toArray(Request $request): array
    {
        /** @var array{kind: string, code: string, discipline_name?: string, discipline_slug?: string, opportunity_id?: int, title?: string} $data */
        $data = $this->data;
        $opportunity = isset($data['opportunity_id']);
        // só MaterialDecided e OpportunityDecided gravam notificações; um tipo desconhecido é erro de programação
        [$title, $detail, $approved] = self::TEXTS[$data['kind']] ?? throw new UnexpectedValueException("Tipo de notificação desconhecido: {$data['kind']}");
        // (bool) explícito: sem ele o Scramble deduz o tipo pelo array de textos e documenta "ok" como string
        $approved = (bool) $approved; // @pest-mutate-ignore: RemoveBooleanCast

        return [
            // o id já é um UUID em texto; os ?? '' abaixo só satisfazem o tipo, porque toda notificação grava esses dados
            'id' => (string) $this->id, // @pest-mutate-ignore: RemoveStringCast
            'title' => $title,
            'detail' => sprintf($detail, $opportunity ? ($data['title'] ?? '') : ($data['discipline_name'] ?? '')), // @pest-mutate-ignore: EmptyStringToNotEmpty
            'ok' => $approved,
            'read' => $this->read_at !== null,
            // aprovado leva ao conteúdo; rejeitado leva ao item em Meus conteúdos (onde está o motivo)
            'link' => match (true) {
                $opportunity && $approved => '/oportunidade#oportunidade-'.$data['opportunity_id'],
                $opportunity => '/perfil/meus-conteudos?tipo=oportunidades&item='.$data['code'],
                $approved => '/estudos/'.($data['discipline_slug'] ?? ''), // @pest-mutate-ignore: EmptyStringToNotEmpty
                default => '/perfil/meus-conteudos?item='.$data['code'],
            },
            'created_at' => $this->created_at?->toIso8601String() ?? '', // @pest-mutate-ignore: RemoveNullSafeOperator,EmptyStringToNotEmpty
        ];
    }
}
