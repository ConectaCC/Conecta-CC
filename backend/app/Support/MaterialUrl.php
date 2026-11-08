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

        if (preg_match('/\s/', $url)) {
            return null;
        }

        // sem protocolo, completa com https://; o que não for domínio (texto solto) o HOST recusa abaixo
        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);
        // os ?? '' só satisfazem o tipo: link sem esquema ou sem host é recusado do mesmo jeito
        $scheme = strtolower($parts['scheme'] ?? ''); // @pest-mutate-ignore: EmptyStringToNotEmpty
        $host = self::canonicalHost($parts['host'] ?? ''); // @pest-mutate-ignore: EmptyStringToNotEmpty

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
        if ($ascii === false) {
            return null;
        }

        // o que volta do idn_to_ascii sempre converte de volta; o false só satisfaz o tipo
        $unicode = idn_to_utf8($ascii, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        return $unicode === false ? null : mb_strtolower($unicode); // @pest-mutate-ignore: FalseToTrue
    }
}
