<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\AuthenticateUser;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;

final class LoginController
{
    public function __invoke(LoginRequest $request, AuthenticateUser $authenticate): UserResource
    {
        // o login do guard já troca o id da sessão e o token CSRF (evita fixação de sessão)
        $user = $authenticate->handle($request->string('email')->value(), $request->string('password')->value());

        return new UserResource($user);
    }
}
