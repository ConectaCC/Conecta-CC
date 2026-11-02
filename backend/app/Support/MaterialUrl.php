<?php

namespace App\Support;

final class MaterialUrl
{
    // rótulos de letras, números e hífen separados por ponto: o mesmo que o navegador consegue abrir
    private const HOST = '/^(?:[\p{L}\p{N}](?:[\p{L}\p{N}-]*[\p{L}\p{N}])?\.)+[\p{L}\p{N}](?:[\p{L}\p{N}-]*[\p{L}\p{N}])?$/u';

    /** Devolve o link pronto para gravar (https:// completado, host em minúsculas) ou null se for inválido. */
    public static function normalize(string $input): ?string
    {
        $url = trim($input);

        if ($url === '' || preg_match('/\s/', $url)) {
            return null;
        }

        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            // sem protocolo, só completa o que parece um domínio (site.com/x), como o protótipo
            if (! preg_match('#^[^\s/:]+\.[^\s]+$#', $url)) {
                return null;
            }
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = mb_strtolower($parts['host'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || ! preg_match(self::HOST, $host) || isset($parts['user'])) {
            return null;
        }

        return $scheme.'://'.$host
            .(isset($parts['port']) ? ':'.$parts['port'] : '')
            .($parts['path'] ?? '')
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }
}
