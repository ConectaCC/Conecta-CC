<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

final class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // só normaliza texto: outro tipo segue como veio e a regra 'string' recusa com 422
        $email = $this->input('email');
        if (is_string($email)) {
            // espaços nas pontas já saem no middleware TrimStrings
            $this->merge(['email' => Str::lower($email)]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'Informe um endereço de e-mail válido.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'password.required' => 'Informe sua senha.',
        ];
    }
}
