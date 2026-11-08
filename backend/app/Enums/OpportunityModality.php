<?php

namespace App\Enums;

enum OpportunityModality: string
{
    case Remoto = 'REMOTO';
    case Hibrido = 'HIBRIDO';
    case Presencial = 'PRESENCIAL';

    public function label(): string
    {
        return match ($this) {
            self::Remoto => 'Remoto',
            self::Hibrido => 'Híbrido',
            self::Presencial => 'Presencial',
        };
    }
}
