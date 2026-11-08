<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class LogoutController
{
    public function __invoke(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        // invalidate() já descarta o token; este só antecipa o novo para a resposta
        $request->session()->regenerateToken(); // @pest-mutate-ignore: RemoveMethodCall

        return response()->noContent();
    }
}
