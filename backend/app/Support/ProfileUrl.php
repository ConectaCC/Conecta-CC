<?php

namespace App\Support;

// só endereços de perfil; query string e fragmento são descartados (spec §5.3)
final class ProfileUrl
{
    public static function github(string $input): ?string
    {
        $parts = self::parse($input, '/^(www\.)?github\.com$/');
        // regra de nome de usuário do GitHub: alfanumérico e hífen, até 39, sem hífen nas pontas nem duplo
        if ($parts === null || ! preg_match('#^/([A-Za-z0-9](?:[A-Za-z0-9]|-(?=[A-Za-z0-9])){0,38})$#', $parts, $match)) {
            return null;
        }

        return 'https://github.com/'.$match[1];
    }

    public static function linkedin(string $input): ?string
    {
        $parts = self::parse($input, '/^(www\.|[a-z]{2}\.)?linkedin\.com$/');
        // nomes acentuados aparecem codificados (jo%C3%A3o) ou crus (joão), conforme de onde o link foi copiado
        if ($parts === null || ! preg_match('#^/in/((?:[\p{L}\p{N}_-]|%[0-9A-Fa-f]{2}){3,100})$#u', $parts, $match)) {
            return null;
        }

        return 'https://www.linkedin.com/in/'.$match[1];
    }

    /** devolve o caminho sem barra final quando o host é aceito; null caso contrário */
    private static function parse(string $input, string $hostPattern): ?string
    {
        $url = trim($input);
        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);
        // o false e os ?? '' só satisfazem o tipo: sem partes, sem esquema ou sem host o link é recusado do mesmo jeito
        if ($parts === false // @pest-mutate-ignore: FalseToTrue
            || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) // @pest-mutate-ignore: EmptyStringToNotEmpty
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])
            || ! preg_match($hostPattern, strtolower($parts['host'] ?? ''))) { // @pest-mutate-ignore: EmptyStringToNotEmpty
            return null;
        }

        // sem caminho, o padrão do perfil recusa: o '' só satisfaz o tipo
        $path = $parts['path'] ?? ''; // @pest-mutate-ignore: EmptyStringToNotEmpty

        return str_ends_with($path, '/') ? substr($path, 0, -1) : $path;
    }
}
