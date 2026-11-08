<?php

namespace App\Http\Resources;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Opportunity */
final class MyOpportunityResource extends JsonResource
{
    /** @return array{id: int, code: string, type: string, title: string, description: string, url: string, modality: string, deadline: string|null, status: string, rejection_reason: string|null, submitted_at: string} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type->value,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'modality' => $this->modality->value,
            'deadline' => $this->deadline?->toDateString(),
            // ENCERRADO: aprovada, mas o prazo passou e ela saiu da página
            'status' => match (true) {
                $this->trashed() => 'EXCLUIDO',
                $this->isExpired() => 'ENCERRADO',
                default => $this->status->value,
            },
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->created_at->toIso8601String(),
        ];
    }
}
