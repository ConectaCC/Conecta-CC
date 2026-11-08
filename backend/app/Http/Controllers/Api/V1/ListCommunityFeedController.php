<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Requests\ListCommunityFeedRequest;
use App\Http\Resources\CommunityFeedItemResource;
use App\Models\Material;
use App\Support\UnaccentSearch;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

// a Comunidade só aponta para o material de Estudos: nenhuma cópia é guardada (decisoes.md §68)
final class ListCommunityFeedController
{
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(ListCommunityFeedRequest $request): AnonymousResourceCollection
    {
        $term = $request->validated('q');
        $direction = $request->validated('order') === 'oldest' ? 'asc' : 'desc';

        $materials = Material::where('status', MaterialStatus::Aprovado)
            ->whereNull('revises_id')
            ->when($term !== null, fn (Builder $query) => $this->search($query, (string) $term))
            ->with(['author', 'discipline'])
            ->orderBy('published_at', $direction)
            ->orderBy('id', $direction)
            ->cursorPaginate(20);

        return CommunityFeedItemResource::collection($materials);
    }

    /** @param  Builder<Material>  $query */
    private function search(Builder $query, string $term): void
    {
        $query->where(function (Builder $query) use ($term): void {
            UnaccentSearch::where($query, 'materials.title', $term)
                ->orWhereHas('discipline', fn (Builder $discipline) => UnaccentSearch::where($discipline, 'disciplines.name', $term))
                // o nome do autor só conta no material identificado: no anônimo, a busca revelaria quem enviou
                ->orWhere(fn (Builder $identified) => $identified
                    ->where('anonymous', false)
                    ->whereHas('author', fn (Builder $author) => UnaccentSearch::where($author, 'users.display_name', $term)));
        });
    }
}
