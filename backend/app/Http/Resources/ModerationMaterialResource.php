<?php

namespace App\Http\Resources;

use App\Enums\Role;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
final class ModerationMaterialResource extends JsonResource
{
    /** @return array{id: int, code: string, title: string, description: string|null, url: string, anonymous: bool, author_name: string|null, is_own: bool, submitted_at: string, discipline: array{id: int, name: string, slug: string}, revision_of: array{title: string, description: string|null, url: string}|null} */
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        // o MODERADOR nunca recebe a identidade do autor anônimo (requisitos §1.2); o ADMINISTRADOR recebe
        // a rota exige sessão: o ?-> só satisfaz o tipo
        $hideAuthor = $this->anonymous && $viewer?->role !== Role::Administrador; // @pest-mutate-ignore: RemoveNullSafeOperator

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'anonymous' => $this->anonymous,
            'author_name' => $hideAuthor ? null : $this->author->display_name,
            'is_own' => $viewer?->id === $this->user_id, // @pest-mutate-ignore: RemoveNullSafeOperator
            'submitted_at' => $this->created_at->toIso8601String(),
            'discipline' => [
                'id' => $this->discipline->id,
                'name' => $this->discipline->name,
                'slug' => $this->discipline->slug,
            ],
            // alteração de material publicado: a versão que está no ar hoje, para comparar
            'revision_of' => $this->revisionOf ? [
                'title' => $this->revisionOf->title,
                'description' => $this->revisionOf->description,
                'url' => $this->revisionOf->url,
            ] : null,
        ];
    }
}
