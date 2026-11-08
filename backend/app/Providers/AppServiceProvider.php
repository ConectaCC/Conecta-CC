<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
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
        // fora de produção, consulta N+1 vira erro (e os testes pegam o eager load que faltou)
        Model::preventLazyLoading(! $this->app->isProduction());

        // id em formato errado na URL dá 404 antes de chegar ao banco (o PostgreSQL daria erro 500)
        Route::pattern('material', '[0-9]+');
        Route::pattern('opportunity', '[0-9]+');
        Route::pattern('discipline', '[0-9]+');
        Route::pattern('student', '[0-9a-f]{16}');
        Route::pattern('notification', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');

        Gate::define('access-moderation', fn (User $user): bool => $user->role->canModerate());

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by(self::emailKey($request).'|'.$request->ip())
                ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)),
            // mesmo trocando de IP, um e-mail não recebe mais de 10 tentativas por minuto
            Limit::perMinute(10)->by('email|'.self::emailKey($request))
                ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)),
        ]);

        // teto geral da API, bem acima do uso normal da SPA: segura raspagem e abuso no plano gratuito
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)
            ->by(self::idKey($request->user()?->getAuthIdentifier()) ?: (string) $request->ip())
            ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)));

        // decisão 8: o usuário é identificado no Sentry só pelo ID interno
        Event::listen(Authenticated::class, function (Authenticated $event): void {
            configureScope(fn (Scope $scope) => $scope->setUser(['id' => self::idKey($event->user->getAuthIdentifier())]));
        });

        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)
            ->by((string) $request->ip())
            ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)));

        RateLimiter::for('password', fn (Request $request) => Limit::perMinute(5)
            ->by(self::idKey($request->user()?->getAuthIdentifier()))
            ->response(fn (Request $request, array $headers) => self::tooManyAttempts($headers)));
    }

    private static function emailKey(Request $request): string
    {
        $email = $request->input('email');

        // o TrimStrings global já tirou os espaços; o e-mail que não é texto a validação recusa
        return is_string($email) ? Str::lower($email) : ''; // @pest-mutate-ignore: EmptyStringToNotEmpty
    }

    private static function idKey(mixed $id): string
    {
        return is_int($id) || is_string($id) ? (string) $id : '';
    }

    /** @param array<array-key, mixed> $headers */
    private static function tooManyAttempts(array $headers): JsonResponse
    {
        // o Laravel sempre manda Retry-After como inteiro: os 0 e o cast só satisfazem o tipo
        $seconds = $headers['Retry-After'] ?? 0; // @pest-mutate-ignore: IncrementInteger,DecrementInteger
        $seconds = is_numeric($seconds) ? (int) $seconds : 0; // @pest-mutate-ignore: RemoveIntegerCast,IncrementInteger,DecrementInteger

        return response()->json(
            ['message' => "Muitas tentativas. Tente novamente em {$seconds} segundos."],
            429,
            $headers,
        );
    }
}
