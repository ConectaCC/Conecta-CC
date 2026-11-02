<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateMaterial;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class StoreMaterialController
{
    public function __invoke(StoreMaterialRequest $request, Discipline $discipline, CreateMaterial $createMaterial): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return (new MaterialResource($createMaterial->handle($user, $discipline, $request->materialData())))
            ->response()
            ->setStatusCode(201);
    }
}
