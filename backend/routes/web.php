<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// a SPA React cuida de todas as rotas que não são da API, do Sanctum, do healthcheck, do painel ou da documentação
Route::get('/{any?}', function () {
    $index = public_path('spa/index.html');
    abort_unless(is_file($index), 404);

    return response(File::get($index))
        ->header('Cache-Control', 'no-cache')
        // a SPA só carrega scripts do próprio site; style-src aceita inline porque o Radix posiciona menus com style=
        ->header('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self' data:", // o Vite embute fontes pequenas como data:
            "connect-src 'self' https://*.sentry.io",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]));
})->where('any', '^(?!api|sanctum|up|admin|docs).*$');
