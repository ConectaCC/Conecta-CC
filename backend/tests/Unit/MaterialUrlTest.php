<?php

use App\Support\MaterialUrl;

it('normaliza o link do material', function (string $input, string $expected) {
    expect(MaterialUrl::normalize($input))->toBe($expected);
})->with([
    'sem protocolo' => ['site.com/x', 'https://site.com/x'],
    'espaços e host em maiúsculas' => ['  https://Site.COM/A?b=1#c ', 'https://site.com/A?b=1#c'],
    'http continua http, com barra' => ['http://site.com', 'http://site.com/'],
    'porta padrão do https some' => ['https://site.com:443/x', 'https://site.com/x'],
    'porta padrão do http some' => ['http://site.com:80/x', 'http://site.com/x'],
    'outra porta continua' => ['https://site.com:8443/x', 'https://site.com:8443/x'],
    'sem caminho ganha barra' => ['https://site.com', 'https://site.com/'],
    'punycode vira acento' => ['https://XN--MNCHEN-3YA.de/x', 'https://münchen.de/x'],
    'acento continua acento' => ['https://München.de/x', 'https://münchen.de/x'],
    'subdomínio sem protocolo' => ['drive.google.com/file/d/1', 'https://drive.google.com/file/d/1'],
    'protocolo em maiúsculas' => ['HTTPS://Site.com/x', 'https://site.com/x'],
    'porta 80 no https continua' => ['https://site.com:80/x', 'https://site.com:80/x'],
    'porta 443 no http continua' => ['http://site.com:443/x', 'http://site.com:443/x'],
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
    'barra vertical no host' => ['https://si|te.com/x'],
    'aspas e tag no host' => ['https://site.com"><script>/'],
    'porcentagem no host' => ['https://si%te.com/'],
    'colchete no host' => ['https://site.com]/x'],
    'host só com ponto' => ['https://./'],
    'rótulo vazio' => ['https://site..com/'],
    'espaço no caminho' => ['https://site.com/a b'],
    'sem host' => ['https:///x'],
    'rótulo com mais de 63 caracteres' => ['https://'.str_repeat('a', 64).'.com/'],
    'sem ponto antes da busca' => ['site?x.com'],
]);

it('aceita host com acento', function () {
    expect(MaterialUrl::normalize('https://Ünï.com/x'))->toBe('https://ünï.com/x');
});
