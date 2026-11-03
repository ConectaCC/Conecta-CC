<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

// byte nulo no texto faz o PostgreSQL recusar a gravação (erro 500); some antes da validação
final class StripNullBytes extends TransformsRequest
{
    protected function transform($key, $value): mixed
    {
        return is_string($value) ? str_replace("\0", '', $value) : $value;
    }
}
