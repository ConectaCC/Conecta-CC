<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Http\Resources\ModerationMaterialResource;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListModerationMaterialsController
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        // shortcut: fila inteira numa resposta; paginar se passar de ~200 pendentes
        $materials = Material::where('status', MaterialStatus::Pendente)
            ->when($user->role !== Role::Administrador, fn ($query) => $query->where('user_id', '!=', $user->id))
            ->with(['author', 'discipline', 'revisionOf'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return ModerationMaterialResource::collection($materials);
    }
}
