<?php

namespace App\Http\Resources;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
final class CommunityFeedItemResource extends JsonResource
{
    /** @return array{id: int, code: string, title: string, url: string, published_at: string|null, discipline: array{name: string, slug: string}, author_name: string|null, author_code: string|null, is_mine: bool} */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'url' => $this->url,
            'published_at' => $this->published_at?->toIso8601String(),
            'discipline' => ['name' => $this->discipline->name, 'slug' => $this->discipline->slug],
            // anônimo: ninguém recebe o autor no mural, nem o ADMINISTRADOR nem o próprio autor (decisoes.md §55.2)
            'author_name' => $this->anonymous ? null : $this->author->display_name,
            'author_code' => $this->anonymous ? null : $this->author->public_code,
            'is_mine' => ! $this->anonymous && $request->user()?->id === $this->user_id,
        ];
    }
}
