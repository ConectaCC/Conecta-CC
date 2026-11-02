<?php

namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Support\MaterialUrl;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

final class StoreMaterialRequest extends FormRequest
{
    // limites da spec da parte 2 (§5.2), maiores que o teto provisório de pendencias.md 2.7
    public const MAX_TITLE = 200;

    public const MAX_DESCRIPTION = 2000;

    public const MAX_URL = 2048;

    public const DUPLICATE_URL = 'Esse link já foi compartilhado nesta disciplina.';

    // na edição, quem não é o autor recebe 403 antes da validação
    public function authorize(): bool
    {
        $material = $this->route('material');

        return ! $material instanceof Material || (bool) $this->user()?->can('manage', $material);
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['title', 'description', 'url'] as $field) {
            if (is_string($this->input($field))) {
                $merge[$field] = trim($this->input($field));
            }
        }

        if (($merge['description'] ?? null) === '') {
            $merge['description'] = null;
        }

        $this->merge($merge);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:'.self::MAX_TITLE],
            'description' => ['nullable', 'string', 'max:'.self::MAX_DESCRIPTION],
            'url' => ['bail', 'required', 'string', 'max:'.self::MAX_URL, $this->urlRule()],
            'anonymous' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'title.required' => 'Informe um título para o material.',
            'title.string' => 'Informe um título para o material.',
            'title.max' => 'Use no máximo 200 caracteres.',
            'description.string' => 'Use no máximo 2.000 caracteres.',
            'description.max' => 'Use no máximo 2.000 caracteres.',
            'url.required' => 'Informe o link do material.',
            'url.string' => 'Informe um link válido.',
            'url.max' => 'Use no máximo 2.048 caracteres.',
        ];
    }

    /**
     * Dados validados, com o link já normalizado.
     *
     * @return array{title: string, description: ?string, url: string, anonymous: bool}
     */
    public function materialData(): array
    {
        /** @var array{title: string, description?: ?string, url: string, anonymous?: bool} $data */
        $data = $this->validated();

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'url' => (string) MaterialUrl::normalize($data['url']),
            'anonymous' => (bool) ($data['anonymous'] ?? false),
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

            // o https:// completado pode passar do tamanho da coluna
            if (mb_strlen($url) > self::MAX_URL) {
                $fail('Use no máximo 2.048 caracteres.');

                return;
            }

            // na edição (PUT /materials/{material}) a disciplina é a do material, que não conta como repetido
            $editing = $this->route('material');
            /** @var Discipline $discipline */
            $discipline = $editing instanceof Material ? $editing->discipline : $this->route('discipline');

            $taken = Material::where('discipline_id', $discipline->id)
                ->where('url', $url)
                ->whereNull('revises_id')
                ->when($editing instanceof Material, fn ($query) => $query->whereKeyNot($editing->id))
                ->whereIn('status', [MaterialStatus::Pendente, MaterialStatus::Aprovado])
                ->exists();

            if ($taken) {
                $fail(self::DUPLICATE_URL);
            }
        };
    }
}
