<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CancelMaterial;
use App\Http\Resources\MyMaterialResource;
use App\Models\Material;
use Illuminate\Support\Facades\Gate;

final class CancelMaterialController
{
    public function __invoke(Material $material, CancelMaterial $cancelMaterial): MyMaterialResource
    {
        Gate::authorize('manage', $material);

        abort_unless($cancelMaterial->handle($material), 409, 'Este envio não pode ser cancelado.');

        return new MyMaterialResource($material->refresh()->load(['discipline', 'revisions']));
    }
}
