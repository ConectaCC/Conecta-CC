<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RejectMaterial;
use App\Http\Requests\RejectMaterialRequest;
use App\Http\Resources\ModerationMaterialResource;
use App\Models\Material;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class RejectMaterialController
{
    public function __invoke(RejectMaterialRequest $request, Material $material, RejectMaterial $rejectMaterial): ModerationMaterialResource
    {
        Gate::authorize('moderate', $material);
        /** @var User $user */
        $user = $request->user();

        abort_unless($rejectMaterial->handle($material, $user, $request->reason()), 409, 'Este conteúdo já foi analisado.');

        return new ModerationMaterialResource($material->refresh()->load(['author', 'discipline']));
    }
}
