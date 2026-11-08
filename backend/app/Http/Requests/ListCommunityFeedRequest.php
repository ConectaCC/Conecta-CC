<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListCommunityFeedRequest extends FormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'in:recent,oldest'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'q.max' => 'Use no máximo 100 caracteres na busca.',
            'order.in' => 'Escolha uma ordem válida.',
        ];
    }
}
