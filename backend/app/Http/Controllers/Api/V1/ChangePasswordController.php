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

        // true apaga já a linha antiga, sem depender da checagem de hash do AuthenticateSession
        // shortcut: resposta em andamento com o cookie antigo pode deslogar este aparelho, trocar se houver relato no piloto
        $session->regenerate(true);

        return response()->noContent();
    }
}
