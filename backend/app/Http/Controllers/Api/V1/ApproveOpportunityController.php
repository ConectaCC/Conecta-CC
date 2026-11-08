<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ApproveOpportunity;
use App\Http\Resources\ModerationOpportunityResource;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class ApproveOpportunityController
{
    public function __invoke(Request $request, Opportunity $opportunity, ApproveOpportunity $action): ModerationOpportunityResource
    {
        Gate::authorize('moderate', $opportunity);
        /** @var User $user */
        $user = $request->user();

        if (! $action->handle($opportunity, $user)) {
            $opportunity->abortDecisionConflict();
        }

        return new ModerationOpportunityResource($opportunity->refresh()->load('author'));
    }
}
