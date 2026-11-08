<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;

final class ShowProfileController
{
    public function __invoke(Request $request): ProfileResource
    {
        /** @var User $user */
        $user = $request->user();

        return new ProfileResource($user->load('disciplines'));
    }
}
