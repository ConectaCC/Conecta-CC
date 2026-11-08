<?php

namespace App\Support;

use Illuminate\Support\Carbon;

// o banco guarda em UTC; datas mostradas e prazos seguem o horário do câmpus (decisoes.md §66.8)
final class Campus
{
    public const TIMEZONE = 'America/Sao_Paulo';

    public static function today(): string
    {
        return Carbon::now(self::TIMEZONE)->toDateString();
    }
}
