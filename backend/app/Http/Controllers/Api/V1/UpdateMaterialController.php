<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ReviseMaterial;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Resources\MyMaterialResource;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

final class UpdateMaterialController
{
    public function __invoke(StoreMaterialRequest $request, Material $material, ReviseMaterial $reviseMaterial): JsonResponse
    {
        Gate::authorize('manage', $material);

        $reviseMaterial->handle($material, $request->materialData());

        return (new MyMaterialResource($material->load(['discipline', 'revisions'])))
            ->response()
            ->setStatusCode(201);
    }
}
