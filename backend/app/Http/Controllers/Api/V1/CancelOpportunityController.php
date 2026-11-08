<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CancelOpportunity;
use App\Http\Resources\MyOpportunityResource;
use App\Models\Opportunity;
use App\Support\Conflict;
use Illuminate\Support\Facades\Gate;

final class CancelOpportunityController
{
    public function __invoke(Opportunity $opportunity, CancelOpportunity $cancelOpportunity): MyOpportunityResource
    {
        Gate::authorize('manage', $opportunity);

        if (! $cancelOpportunity->handle($opportunity)) {
            Conflict::abort('not_cancellable', 'Este envio não pode ser cancelado.');
        }

        return new MyOpportunityResource($opportunity->refresh());
    }
}
