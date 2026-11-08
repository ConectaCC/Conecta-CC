<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\StripNullBytes;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->append(StripNullBytes::class);
        $middleware->append(SecurityHeaders::class);
        // o Render termina o HTTPS no proxy: só o protocolo e a porta vêm dele. O IP real o nginx da imagem já resolve
        // (confia no Cloudflare e na rede do Render), e o host vem do próprio pedido, nunca do X-Forwarded-Host
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
        $middleware->throttleApi('api');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        // a mensagem padrão do 404 cita o modelo procurado ("No query results for model [App\Models\User]") e sai em inglês
        $exceptions->render(fn (NotFoundHttpException $e, Request $request) => $request->is('api/*')
            ? response()->json(['message' => 'Não encontramos o que você procurou.'], 404)
            : null);
        $exceptions->render(fn (MethodNotAllowedHttpException $e, Request $request) => $request->is('api/*')
            ? response()->json(['message' => 'Esta ação não é permitida neste endereço.'], 405, $e->getHeaders())
            : null);
    })->create();
