<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Http\Resources\ModerationOpportunityResource;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListModerationOpportunitiesController
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        // shortcut: fila inteira numa resposta, como a de materiais; paginar se passar de ~200 pendentes
        $opportunities = Opportunity::where('status', MaterialStatus::Pendente)
            ->when($user->role !== Role::Administrador, fn ($query) => $query->where('user_id', '!=', $user->id))
            ->with('author')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return ModerationOpportunityResource::collection($opportunities);
    }
}
