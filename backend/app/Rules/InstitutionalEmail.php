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
        $domain = Str::lower(Str::afterLast(is_string($value) ? $value : '', '@'));

        if (! in_array($domain, config()->array('conectacc.allowed_email_domains'), true)) {
            $fail('Use seu e-mail institucional do IFSul.');
        }
    }
}
