<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Http\Resources\MaterialResource;
use App\Models\Discipline;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListDisciplineMaterialsController
{
    public function __invoke(Discipline $discipline): AnonymousResourceCollection
    {
        $materials = $discipline->materials()
            ->where('status', MaterialStatus::Aprovado)
            ->whereNull('revises_id')
            ->with('author')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->cursorPaginate(20);

        return MaterialResource::collection($materials);
    }
}
