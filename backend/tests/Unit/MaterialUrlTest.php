<?php

use App\Support\MaterialUrl;

it('normaliza o link do material', function (string $input, string $expected) {
    expect(MaterialUrl::normalize($input))->toBe($expected);
})->with([
    'sem protocolo' => ['site.com/x', 'https://site.com/x'],
    'espaços e host em maiúsculas' => ['  https://Site.COM/A?b=1#c ', 'https://site.com/A?b=1#c'],
    'http continua http' => ['http://site.com', 'http://site.com'],
    'subdomínio sem protocolo' => ['drive.google.com/file/d/1', 'https://drive.google.com/file/d/1'],
]);

it('recusa link inválido', function (string $input) {
    expect(MaterialUrl::normalize($input))->toBeNull();
})->with([
    'javascript' => ['javascript:alert(1)'],
    'ftp' => ['ftp://site.com'],
    'espaço no host' => ['https://site .com'],
    'sem ponto no host' => ['localhost'],
    'vazio' => [''],
    'só espaços' => ['   '],
    'texto solto' => ['meu material'],
]);
