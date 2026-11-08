<?php

namespace App\Http\Requests;

use App\Rules\InstitutionalEmail;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

final class RegisterRequest extends FormRequest
{
    public const EMAIL_TAKEN = 'Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.';

    private const TERMS_OUTDATED = 'Os Termos de participação foram atualizados. Leia e aceite novamente.';

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
            'name' => ['required', 'string', 'max:120'],
            'email' => ['bail', 'required', 'email', new InstitutionalEmail, 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            // closure em vez de Rule::in: a versão vigente é configuração e não deve virar enum no OpenAPI
            'terms_version' => ['required', function (string $attribute, mixed $value, Closure $fail): void {
                if ($value !== config('conectacc.terms_version')) {
                    $fail(self::TERMS_OUTDATED);
                }
            }],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Informe seu nome completo.',
            'name.max' => 'Use no máximo 120 caracteres.',
            'email.required' => 'Informe um endereço de e-mail válido.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => self::EMAIL_TAKEN,
            'password.required' => 'Use pelo menos 8 caracteres.',
            'password.min' => 'Use pelo menos 8 caracteres.',
            'password_confirmation.required' => 'As senhas informadas não coincidem.',
            'password_confirmation.same' => 'As senhas informadas não coincidem.',
            'terms_version.required' => self::TERMS_OUTDATED,
        ];
    }
}
