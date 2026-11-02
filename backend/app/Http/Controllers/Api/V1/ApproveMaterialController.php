<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ApproveMaterial;
use App\Http\Resources\ModerationMaterialResource;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class ApproveMaterialController
{
    public function __invoke(Request $request, Material $material, ApproveMaterial $approveMaterial): ModerationMaterialResource
    {
        Gate::authorize('moderate', $material);
        /** @var User $user */
        $user = $request->user();

        abort_unless($approveMaterial->handle($material, $user), 409, 'Este conteúdo já foi analisado.');

        return new ModerationMaterialResource($material->refresh()->load(['author', 'discipline']));
    }
}
