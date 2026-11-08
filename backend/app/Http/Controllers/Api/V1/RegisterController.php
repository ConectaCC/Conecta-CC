<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RegisterUser;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;

final class RegisterController
{
    public function __invoke(RegisterRequest $request, RegisterUser $registerUser): JsonResponse
    {
        $registerUser->handle(
            $request->string('name')->value(),
            $request->string('email')->value(),
            $request->string('password')->value(),
            $request->string('terms_version')->value(),
        );

        return response()->json(['message' => 'Conta criada.'], 201);
    }
}
