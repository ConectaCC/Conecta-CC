<?php

namespace App\Http\Requests;

use App\Enums\OpportunityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListOpportunitiesRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(OpportunityType::class)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'q.max' => 'Use no máximo 100 caracteres na busca.',
            'type.enum' => 'Escolha um tipo válido.',
        ];
    }
}
