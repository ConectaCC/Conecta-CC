<?php

namespace App\Filament\Support;

use App\Models\User;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

// o provedor padrão do Filament busca o avatar em ui-avatars.com, enviando as iniciais da pessoa para fora
final class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initials = $record instanceof User ? e($record->initials()) : '';

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44">'
            .'<rect width="44" height="44" fill="#d6e4f7"/>'
            .'<text x="22" y="27" text-anchor="middle" font-family="sans-serif" font-size="14" font-weight="600" fill="#071f43">'.$initials.'</text>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
