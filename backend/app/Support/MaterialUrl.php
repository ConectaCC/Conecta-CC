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
        $host = self::canonicalHost($parts['host'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || $host === null || ! preg_match(self::HOST, $host) || isset($parts['user'])) {
            return null;
        }

        // porta padrão e caminho vazio não mudam o endereço: somem para o link repetido ser reconhecido
        $port = $parts['port'] ?? null;
        $defaultPort = ($scheme === 'https' && $port === 443) || ($scheme === 'http' && $port === 80);

        return $scheme.'://'.$host
            .($port !== null && ! $defaultPort ? ':'.$port : '')
            .(($parts['path'] ?? '') === '' ? '/' : $parts['path'])
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    // "XN--MNCHEN-3YA.de" e "München.de" viram o mesmo "münchen.de"
    private static function canonicalHost(string $host): ?string
    {
        if ($host === '') {
            return null;
        }

        $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $unicode = $ascii === false ? false : idn_to_utf8($ascii, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        return $unicode === false ? null : mb_strtolower($unicode);
    }
}
