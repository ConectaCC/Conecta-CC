<?php

namespace App\Http\Resources;

use App\Enums\Role;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
final class ModerationMaterialResource extends JsonResource
{
    /** @return array{id: int, code: string, title: string, description: string|null, url: string, anonymous: bool, author_name: string|null, is_own: bool, submitted_at: string, discipline: array{id: int, name: string, slug: string}} */
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        // o MODERADOR nunca recebe a identidade do autor anônimo (requisitos §1.2); o ADMINISTRADOR recebe
        $hideAuthor = $this->anonymous && $viewer?->role !== Role::Administrador;

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'anonymous' => $this->anonymous,
            'author_name' => $hideAuthor ? null : $this->author->display_name,
            'is_own' => $viewer?->id === $this->user_id,
            'submitted_at' => $this->created_at->toIso8601String(),
            'discipline' => [
                'id' => $this->discipline->id,
                'name' => $this->discipline->name,
                'slug' => $this->discipline->slug,
            ],
        ];
    }
}
