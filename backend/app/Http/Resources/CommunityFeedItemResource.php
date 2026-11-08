<?php

namespace App\Http\Resources;

use App\Models\Material;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property object{kind: string, model: Material|Opportunity} $resource */
final class CommunityFeedItemResource extends JsonResource
{
    /** @return array{kind: string, id: int, code: string, title: string, url: string, published_at: string|null, discipline: array{name: string, slug: string}|null, opportunity_type: string|null, author_name: string|null, author_code: string|null, is_mine: bool} */
    public function toArray(Request $request): array
    {
        /** @var Material|Opportunity $item */
        $item = $this->resource->model;
        $opportunity = $item instanceof Opportunity ? $item : null;
        $material = $item instanceof Material ? $item : null;
        // material anônimo e oportunidade não têm autoria pública (decisoes.md §55.2 e §70)
        $author = $material !== null && ! $material->anonymous ? $material->author : null;

        return [
            'kind' => $opportunity !== null ? 'opportunity' : 'material',
            'id' => (int) $item->id,
            'code' => (string) $item->code,
            'title' => (string) $item->title,
            'url' => (string) $item->url,
            'published_at' => $item->published_at?->toIso8601String(),
            'discipline' => $material !== null ? ['name' => $material->discipline->name, 'slug' => $material->discipline->slug] : null,
            'opportunity_type' => $opportunity?->type->value,
            'author_name' => $author?->display_name,
            'author_code' => $author?->public_code,
            'is_mine' => (bool) ($author !== null && $request->user()?->id === $author->id),
        ];
    }
}
