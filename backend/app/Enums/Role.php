<?php

namespace App\Enums;

enum Role: string
{
    case Aluno = 'ALUNO';
    case Moderador = 'MODERADOR';
    case Administrador = 'ADMINISTRADOR';

    // sem herança automática entre papéis: cada permissão é explícita (requisitos.md §1)
    public function canModerate(): bool
    {
        return match ($this) {
            self::Moderador, self::Administrador => true,
            self::Aluno => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Aluno => 'Aluno',
            self::Moderador => 'Moderador',
            self::Administrador => 'Administrador',
        };
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $role) => [$role->value => $role->label()])->all();
    }
}
