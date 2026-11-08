<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateOpportunity;
use App\Http\Requests\StoreOpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\User;
use App\Support\MaterialSubmissionLimit;
use Illuminate\Http\JsonResponse;

final class StoreOpportunityController
{
    public function __invoke(StoreOpportunityRequest $request, CreateOpportunity $createOpportunity): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $opportunity = $createOpportunity->handle($user, $request->opportunityData());
        MaterialSubmissionLimit::hit($user);

        return (new OpportunityResource($opportunity))->response()->setStatusCode(201);
    }
}
