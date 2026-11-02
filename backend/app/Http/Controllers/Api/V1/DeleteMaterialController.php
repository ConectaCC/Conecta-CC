<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\DeleteMaterial;
use App\Models\Material;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class DeleteMaterialController
{
    public function __invoke(Material $material, DeleteMaterial $deleteMaterial): Response
    {
        Gate::authorize('manage', $material);

        $deleteMaterial->handle($material);

        return response()->noContent();
    }
}
