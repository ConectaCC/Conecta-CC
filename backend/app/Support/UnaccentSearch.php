<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Busca sem acentos e sem diferenciar maiúsculas, com % e _ tratados como texto.
 *
 * shortcut: unaccent sem índice percorre a tabela inteira; criar um índice com expressão se as buscas ficarem lentas
 */
final class UnaccentSearch
{
    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @param  literal-string  $column  nome de coluna escrito no código, nunca vindo da requisição
     * @return Builder<TModel>
     */
    public static function where(Builder $query, string $column, string $term, string $boolean = 'and'): Builder
    {
        $pattern = '%'.addcslashes(mb_strtolower($term), '\\%_').'%';

        return $query->whereRaw("unaccent(lower({$column})) LIKE unaccent(?)", [$pattern], $boolean);
    }
}
