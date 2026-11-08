<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ListOpportunitiesRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Support\UnaccentSearch;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListOpportunitiesController
{
    #[QueryParameter('cursor', description: 'Cursor da próxima página (meta.next_cursor).', type: 'string')]
    public function __invoke(ListOpportunitiesRequest $request): AnonymousResourceCollection
    {
        $term = $request->string('q')->value();
        $type = $request->validated('type');

        $opportunities = Opportunity::query()
            ->visible()
            ->when($type !== null, fn (Builder $query) => $query->where('type', $type))
            ->when($term !== '', fn (Builder $query) => $query->where(fn (Builder $query) => UnaccentSearch::where(
                UnaccentSearch::where($query, 'opportunities.title', $term),
                'opportunities.description',
                $term,
                'or',
            )))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->cursorPaginate(20);

        return OpportunityResource::collection($opportunities);
    }
}
