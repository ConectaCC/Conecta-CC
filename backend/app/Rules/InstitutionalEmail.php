<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

final class InstitutionalEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // texto depois do último @, comparado por igualdade: subdomínios e sufixos não passam
        // a regra 'email', antes desta com bail, já recusa o que não é texto
        $domain = Str::lower(Str::afterLast(is_string($value) ? $value : '', '@')); // @pest-mutate-ignore: EmptyStringToNotEmpty

        if (! in_array($domain, config()->array('conectacc.allowed_email_domains'), true)) {
            $fail('Use seu e-mail institucional do IFSul.');
        }
    }
}
