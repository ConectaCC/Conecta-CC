<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RejectMaterialRequest extends FormRequest
{
    public const MAX_REASON = 500;

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('reason'))) {
            $this->merge(['reason' => trim($this->input('reason'))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:'.self::MAX_REASON]];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'reason.required' => 'Informe o motivo da rejeição.',
            'reason.string' => 'Informe o motivo da rejeição.',
            'reason.max' => 'Use no máximo 500 caracteres.',
        ];
    }

    public function reason(): string
    {
        return (string) $this->validated('reason');
    }
}
