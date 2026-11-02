<?php

namespace App\Support;

// 409 com um código estável: o front decide pelo código, nunca pelo texto da mensagem
final class Conflict
{
    public static function abort(string $code, string $message): never
    {
        abort(response()->json(['message' => $message, 'code' => $code], 409));
    }
}
