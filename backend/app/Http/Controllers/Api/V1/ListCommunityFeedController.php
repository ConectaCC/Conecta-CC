<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Resources\CommunityFeedItemResource;
use App\Models\Material;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

// a Comunidade só aponta para o material de Estudos: nenhuma cópia é guardada (decisoes.md §68)
final class ListCommunityFeedController
{
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(): AnonymousResourceCollection
    {
        $materials = Material::where('status', MaterialStatus::Aprovado)
            ->whereNull('revises_id')
            ->with(['author', 'discipline'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->cursorPaginate(20);

        return CommunityFeedItemResource::collection($materials);
    }
}
