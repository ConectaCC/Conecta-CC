<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Requests\ListCommunityFeedRequest;
use App\Http\Resources\CommunityFeedItemResource;
use App\Models\Material;
use App\Models\Opportunity;
use App\Support\UnaccentSearch;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

// a Comunidade só aponta para os conteúdos de Estudos e de Oportunidade: nenhuma cópia é guardada (decisoes.md §68)
final class ListCommunityFeedController
{
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(ListCommunityFeedRequest $request): AnonymousResourceCollection
    {
        $term = $request->string('q')->value();
        $type = $request->validated('type');
        $direction = $request->validated('order') === 'oldest' ? 'asc' : 'desc';

        $materials = Material::query()
            ->selectRaw("'material' as kind, id, published_at")
            ->where('status', MaterialStatus::Aprovado)
            ->whereNull('revises_id')
            // buscar '' casa com tudo: o when só poupa a condição
            ->when($term !== '', fn (Builder $query) => $this->searchMaterials($query, $term)); // @pest-mutate-ignore: EmptyStringToNotEmpty

        $opportunities = Opportunity::query()
            ->selectRaw("'opportunity' as kind, id, published_at")
            ->visible()
            ->when($term !== '', fn (Builder $query) => $query->where(fn (Builder $query) => UnaccentSearch::where( // @pest-mutate-ignore: EmptyStringToNotEmpty
                UnaccentSearch::where($query, 'opportunities.title', $term),
                'opportunities.description',
                $term,
                'or',
            )));

        $union = match ($type) {
            'material' => $materials->toBase(),
            'opportunity' => $opportunities->toBase(),
            default => $materials->toBase()->unionAll($opportunities->toBase()),
        };

        // kind entra na ordem para desempatar item de tabelas diferentes publicado no mesmo instante
        /** @var CursorPaginator<int, \stdClass> $page */
        $page = DB::query()->fromSub($union, 'feed')
            ->orderBy('published_at', $direction)
            ->orderBy('kind', $direction)
            ->orderBy('id', $direction)
            ->cursorPaginate(20);

        $rows = collect($page->items());
        $loaded = [
            'material' => Material::with(['author', 'discipline'])->findMany($rows->where('kind', 'material')->pluck('id'))->keyBy('id'),
            'opportunity' => Opportunity::findMany($rows->where('kind', 'opportunity')->pluck('id'))->keyBy('id'),
        ];

        // a linha original fica no item: o cursor da próxima página é calculado a partir de kind, id e published_at
        return CommunityFeedItemResource::collection($page->through(function (\stdClass $row) use ($loaded): \stdClass {
            $kind = $row->kind === 'opportunity' ? 'opportunity' : 'material';
            // o id vem do banco sempre numérico: o cast e o 0 só satisfazem o tipo
            $row->model = $loaded[$kind][is_numeric($row->id) ? (int) $row->id : 0]; // @pest-mutate-ignore: RemoveIntegerCast,IncrementInteger,DecrementInteger

            return $row;
        }));
    }

    /** @param  Builder<Material>  $query */
    private function searchMaterials(Builder $query, string $term): void
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
