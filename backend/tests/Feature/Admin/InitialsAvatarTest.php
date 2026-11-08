<?php

use App\Filament\Support\InitialsAvatarProvider;
use App\Models\Discipline;
use App\Models\User;

function avatarSvg(mixed $record): string
{
    $url = (new InitialsAvatarProvider)->get($record);
    expect($url)->toStartWith('data:image/svg+xml;base64,');

    return base64_decode(substr($url, strlen('data:image/svg+xml;base64,')));
}

it('desenha as iniciais num SVG local, sem serviço externo', function () {
    $svg = avatarSvg(User::factory()->make(['display_name' => 'Ana Silva']));

    expect($svg)->toStartWith('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44"><rect ')
        ->toContain('fill="#071f43">AS</text></svg>')
        ->toEndWith('</svg>')
        ->not->toContain('ui-avatars');
});

it('escapa o nome no SVG', function () {
    expect(avatarSvg(User::factory()->make(['display_name' => '<b> &x'])))->toContain('>&lt;&amp;</text>');
});

it('fica sem iniciais para outro tipo de registro', function () {
    expect(avatarSvg(new Discipline))->toContain('fill="#071f43"></text>');
});
