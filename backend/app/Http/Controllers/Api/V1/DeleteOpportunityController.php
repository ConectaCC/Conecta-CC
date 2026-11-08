<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\DeleteOpportunity;
use App\Models\Opportunity;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class DeleteOpportunityController
{
    public function __invoke(Opportunity $opportunity, DeleteOpportunity $deleteOpportunity): Response
    {
        Gate::authorize('manage', $opportunity);

        $deleteOpportunity->handle($opportunity);

        return response()->noContent();
    }
}
