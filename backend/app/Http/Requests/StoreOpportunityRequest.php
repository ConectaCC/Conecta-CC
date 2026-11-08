<?php

namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use App\Enums\OpportunityModality;
use App\Enums\OpportunityType;
use App\Models\Opportunity;
use App\Support\Campus;
use App\Support\MaterialSubmissionLimit;
use App\Support\MaterialUrl;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreOpportunityRequest extends FormRequest
{
    public const MAX_TITLE = 200;

    public const MAX_DESCRIPTION = 300;

    public const MAX_URL = 2048;

    public const DUPLICATE_URL = 'Essa oportunidade já foi compartilhada.';

    protected function prepareForValidation(): void
    {
        // o mesmo limite de envios por hora do material: os dois tipos somam
        MaterialSubmissionLimit::ensureAvailable($this->user());

        $merge = [];
        foreach (['title', 'description', 'url'] as $field) {
            if (is_string($this->input($field))) {
                $merge[$field] = trim($this->input($field));
            }
        }
        $this->merge($merge);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(OpportunityType::class)],
            'title' => ['required', 'string', 'max:'.self::MAX_TITLE],
            'description' => ['required', 'string', 'max:'.self::MAX_DESCRIPTION],
            'url' => ['bail', 'required', 'string', 'max:'.self::MAX_URL, $this->urlRule()],
            'modality' => ['required', Rule::enum(OpportunityModality::class)],
            'deadline' => ['bail', 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.Campus::today()],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'type.required' => 'Escolha o tipo da oportunidade.',
            'type.enum' => 'Escolha o tipo da oportunidade.',
            'title.required' => 'Informe um título para a oportunidade.',
            'title.string' => 'Informe um título para a oportunidade.',
            'title.max' => 'Use no máximo 200 caracteres.',
            'description.required' => 'Descreva a oportunidade em poucas palavras.',
            'description.string' => 'Descreva a oportunidade em poucas palavras.',
            'description.max' => 'Use no máximo 300 caracteres.',
            'url.required' => 'Informe o link da oportunidade.',
            'url.string' => 'Informe um link válido.',
            'url.max' => 'Use no máximo 2.048 caracteres.',
            'modality.required' => 'Escolha a modalidade.',
            'modality.enum' => 'Escolha a modalidade.',
            'deadline.date_format' => 'Informe uma data válida.',
            'deadline.after_or_equal' => 'Escolha uma data a partir de hoje.',
        ];
    }

    /** @return array{type: string, title: string, description: string, url: string, modality: string, deadline: ?string} */
    public function opportunityData(): array
    {
        /** @var array{type: string, title: string, description: string, url: string, modality: string, deadline?: ?string} $data */
        $data = $this->validated();

        return [
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'],
            'url' => (string) MaterialUrl::normalize($data['url']),
            'modality' => $data['modality'],
            'deadline' => $data['deadline'] ?? null,
        ];
    }

    private function urlRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $url = MaterialUrl::normalize((string) $value);

            if ($url === null) {
                $fail('Informe um link válido.');

                return;
            }

            if (mb_strlen($url) > self::MAX_URL) {
                $fail('Use no máximo 2.048 caracteres.');

                return;
            }

            if (Opportunity::where('url', $url)->whereIn('status', [MaterialStatus::Pendente, MaterialStatus::Aprovado])->exists()) {
                $fail(self::DUPLICATE_URL);
            }
        };
    }
}
