<?php

namespace App\Http\Resources;

use App\Enums\MaterialStatus;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
final class MyMaterialResource extends JsonResource
{
    /**
     * @return array{id: int, code: string, title: string, description: string|null, url: string, anonymous: bool, status: string, rejection_reason: string|null, submitted_at: string, discipline: array{id: int, name: string, slug: string}, pending_revision: array{title: string, description: string|null, url: string, submitted_at: string}|null, rejected_revision: array{reason: string|null, decided_at: string|null}|null}
     */
    public function toArray(Request $request): array
    {
        // só pode haver uma alteração pendente, e ela é sempre a mais nova
        $latest = $this->revisions->sortByDesc('id')->first();

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'anonymous' => $this->anonymous,
            'status' => $this->trashed() ? 'EXCLUIDO' : $this->status->value,
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => $this->created_at->toIso8601String(),
            'discipline' => [
                'id' => $this->discipline->id,
                'name' => $this->discipline->name,
                'slug' => $this->discipline->slug,
            ],
            'pending_revision' => $latest?->status === MaterialStatus::Pendente ? [
                'title' => $latest->title,
                'description' => $latest->description,
                'url' => $latest->url,
                'submitted_at' => $latest->created_at->toIso8601String(),
            ] : null,
            'rejected_revision' => $latest?->status === MaterialStatus::Rejeitado ? [
                'reason' => $latest->rejection_reason,
                'decided_at' => $latest->decided_at?->toIso8601String(),
            ] : null,
        ];
    }
}
