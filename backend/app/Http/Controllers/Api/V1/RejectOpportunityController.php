<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RejectOpportunity;
use App\Http\Requests\RejectMaterialRequest;
use App\Http\Resources\ModerationOpportunityResource;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class RejectOpportunityController
{
    public function __invoke(RejectMaterialRequest $request, Opportunity $opportunity, RejectOpportunity $action): ModerationOpportunityResource
    {
        Gate::authorize('moderate', $opportunity);
        /** @var User $user */
        $user = $request->user();

        if (! $action->handle($opportunity, $user, $request->reason())) {
            $opportunity->abortDecisionConflict();
        }

        return new ModerationOpportunityResource($opportunity->refresh()->load('author'));
    }
}
