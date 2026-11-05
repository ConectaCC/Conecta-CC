<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ChangePassword;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Response;

final class ChangePasswordController
{
    public function __invoke(ChangePasswordRequest $request, ChangePassword $changePassword): Response
    {
        $session = $request->session();
        $changePassword->handle($request->user(), $request->string('password')->value(), $session->getId());

        // true apaga o id antigo: um cookie roubado não pode sobreviver à troca
        $session->regenerate(true);

        return response()->noContent();
    }
}
