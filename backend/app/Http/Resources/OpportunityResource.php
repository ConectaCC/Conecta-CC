<?php

namespace App\Http\Resources;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Opportunity */
final class OpportunityResource extends JsonResource
{
    /** @return array{id: int, code: string, type: string, title: string, description: string, url: string, modality: string, deadline: string|null, published_at: string|null} */
    public function toArray(Request $request): array
    {
        // sem autoria: a área pública nunca mostra quem enviou a oportunidade (decisoes.md §70)
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type->value,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'modality' => $this->modality->value,
            'deadline' => $this->deadline?->toDateString(),
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
