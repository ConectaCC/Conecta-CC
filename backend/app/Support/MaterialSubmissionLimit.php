<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

// 10 envios ou edições por hora; só conta o que deu certo (erro de validação não gasta a cota)
final class MaterialSubmissionLimit
{
    private const MAX = 10;

    private const DECAY_SECONDS = 3600;

    public static function ensureAvailable(?User $user): void
    {
        if ($user === null || ! RateLimiter::tooManyAttempts(self::key($user), self::MAX)) {
            return;
        }

        $seconds = RateLimiter::availableIn(self::key($user));
        abort(response()->json(['message' => "Muitas tentativas. Tente novamente em {$seconds} segundos."], 429, ['Retry-After' => $seconds]));
    }

    public static function hit(User $user): void
    {
        RateLimiter::hit(self::key($user), self::DECAY_SECONDS);
    }

    private static function key(User $user): string
    {
        // o prefixo só separa de outras chaves brutas do RateLimiter; hoje nenhuma colide
        return 'materials|'.$user->id; // @pest-mutate-ignore: ConcatRemoveLeft,ConcatSwitchSides
    }
}
