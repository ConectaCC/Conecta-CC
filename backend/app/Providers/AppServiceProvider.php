<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Sentry\State\Scope;

use function Sentry\configureScope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        // id em formato errado na URL dá 404 antes de chegar ao banco (o PostgreSQL daria erro 500)
        Route::pattern('material', '[0-9]+');
        Route::pattern('discipline', '[0-9]+');
        Route::pattern('student', '[0-9a-f]{16}');
        Route::pattern('notification', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');

        Gate::define('access-moderation', fn (User $user): bool => $user->role->canModerate());

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::lower(trim((string) $request->input('email'))).'|'.$request->ip())
            ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)));

        // decisão 8: o usuário é identificado no Sentry só pelo ID interno
        Event::listen(Authenticated::class, function (Authenticated $event): void {
            configureScope(fn (Scope $scope) => $scope->setUser(['id' => (string) $event->user->getAuthIdentifier()]));
        });

        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)
            ->by((string) $request->ip())
            ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)));
    }

    /** @param array<string, string|int> $headers */
    private static function tooManyAttempts(array $headers): JsonResponse
    {
        return response()->json(
            ['message' => "Muitas tentativas. Tente novamente em {$headers['Retry-After']} segundos."],
            429,
            $headers,
        );
    }
}
