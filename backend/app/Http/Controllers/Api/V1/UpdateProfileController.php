<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpdateProfile;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;

final class UpdateProfileController
{
    public function __invoke(UpdateProfileRequest $request, UpdateProfile $updateProfile): ProfileResource
    {
        /** @var User $user */
        $user = $request->user();

        return new ProfileResource($updateProfile->handle($user, $request->profileData())->load('disciplines'));
    }
}
