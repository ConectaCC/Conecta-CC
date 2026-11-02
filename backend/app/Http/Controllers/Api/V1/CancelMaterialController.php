<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CancelMaterial;
use App\Http\Resources\MyMaterialResource;
use App\Models\Material;
use App\Support\Conflict;
use Illuminate\Support\Facades\Gate;

final class CancelMaterialController
{
    public function __invoke(Material $material, CancelMaterial $cancelMaterial): MyMaterialResource
    {
        Gate::authorize('manage', $material);

        if (! $cancelMaterial->handle($material)) {
            Conflict::abort('not_cancellable', 'Este envio não pode ser cancelado.');
        }

        return new MyMaterialResource($material->refresh()->load(['discipline', 'revisions']));
    }
}
