<?php

namespace App\Enums;

// os tipos do RF-43 e da decisão §22, sem criação livre
enum OpportunityType: string
{
    case Estagio = 'ESTAGIO';
    case Emprego = 'EMPREGO';
    case Evento = 'EVENTO';
    case Projeto = 'PROJETO';
    case Outro = 'OUTRO';

    public function label(): string
    {
        return match ($this) {
            self::Estagio => 'Estágio',
            self::Emprego => 'Emprego',
            self::Evento => 'Evento',
            self::Projeto => 'Projeto',
            self::Outro => 'Outro',
        };
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type) => [$type->value => $type->label()])->all();
    }
}
