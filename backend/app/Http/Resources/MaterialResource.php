<?php

namespace App\Http\Resources;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
final class MaterialResource extends JsonResource
{
    /** @return array{id: int, code: string, title: string, description: string|null, url: string, author_name: string|null, published_at: string|null, is_mine: bool} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            // anônimo: ninguém recebe o nome na área pública, nem o ADMINISTRADOR (requisitos §1.3)
            'author_name' => $this->anonymous ? null : $this->author->display_name,
            'published_at' => $this->published_at?->toIso8601String(),
            'is_mine' => $request->user()?->id === $this->user_id,
        ];
    }
}
