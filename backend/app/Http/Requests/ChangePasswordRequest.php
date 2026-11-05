<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ChangePasswordRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'current_password' => ['bail', 'required', 'string', 'current_password:web'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Informe sua senha atual.',
            'current_password.current_password' => 'A senha atual informada está incorreta.',
            'password.required' => 'Informe sua nova senha.',
            'password.min' => 'Use pelo menos 8 caracteres.',
            'password_confirmation.required' => 'Confirme sua nova senha.',
            'password_confirmation.same' => 'As senhas informadas não coincidem.',
        ];
    }
}
