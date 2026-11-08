<?php

namespace App\Http\Resources;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Opportunity */
final class ModerationOpportunityResource extends JsonResource
{
    /** @return array{id: int, code: string, type: string, title: string, description: string, url: string, modality: string, deadline: string|null, author_name: string, is_own: bool, submitted_at: string} */
    public function toArray(Request $request): array
    {
        // a rota exige sessão: o ?-> só satisfaz o tipo
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type->value,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'modality' => $this->modality->value,
            'deadline' => $this->deadline?->toDateString(),
            // na análise, quem modera sempre sabe quem enviou: oportunidade não tem envio anônimo (decisoes.md §70)
            'author_name' => $this->author->display_name,
            'is_own' => $request->user()?->id === $this->user_id, // @pest-mutate-ignore: RemoveNullSafeOperator
            'submitted_at' => $this->created_at->toIso8601String(),
        ];
    }
}
