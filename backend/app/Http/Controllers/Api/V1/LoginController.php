<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\AuthenticateUser;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;

final class LoginController
{
    public function __invoke(LoginRequest $request, AuthenticateUser $authenticate): UserResource
    {
        $user = $authenticate->handle($request->string('email')->value(), $request->string('password')->value());

        $request->session()->regenerate(); // evita fixação de sessão

        return new UserResource($user);
    }
}
