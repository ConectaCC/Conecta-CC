<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Requests\ListCommunityFeedRequest;
use App\Http\Resources\CommunityFeedItemResource;
use App\Models\Material;
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

    /**
     * shortcut: unaccent sem índice percorre a tabela inteira; criar um índice com expressão se o mural ficar lento
     *
     * @param  Builder<Material>  $query
     */
    private function search(Builder $query, string $term): void
    {
        $pattern = '%'.addcslashes(mb_strtolower($term), '\\%_').'%';
        $matches = fn (string $column) => ["unaccent(lower({$column})) LIKE unaccent(?)", [$pattern]];

        $query->where(function (Builder $query) use ($matches): void {
            $query->whereRaw(...$matches('materials.title'))
                ->orWhereHas('discipline', fn (Builder $discipline) => $discipline->whereRaw(...$matches('disciplines.name')))
                // o nome do autor só conta no material identificado: no anônimo, a busca revelaria quem enviou
                ->orWhere(fn (Builder $identified) => $identified
                    ->where('anonymous', false)
                    ->whereHas('author', fn (Builder $author) => $author->whereRaw(...$matches('users.display_name'))));
        });
    }
}
