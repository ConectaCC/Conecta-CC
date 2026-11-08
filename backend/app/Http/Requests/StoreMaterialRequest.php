<?php

namespace App\Http\Requests;

use App\Enums\MaterialStatus;
use App\Models\Discipline;
use App\Models\Material;
use App\Support\MaterialSubmissionLimit;
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

        // a rota exige sessão: o ?-> só satisfaz o tipo
        return ! $material instanceof Material || (bool) $this->user()?->can('manage', $material); // @pest-mutate-ignore: RemoveBooleanCast,RemoveNullSafeOperator
    }

    protected function prepareForValidation(): void
    {
        MaterialSubmissionLimit::ensureAvailable($this->user());
        // espaços nas pontas e texto vazio já chegam tratados pelos middlewares TrimStrings e ConvertEmptyStringsToNull
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:'.self::MAX_TITLE],
            'description' => ['nullable', 'string', 'max:'.self::MAX_DESCRIPTION],
            'url' => ['required', $this->urlRule()],
            'anonymous' => ['boolean'],
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
            // a validação já garantiu um link válido: o cast só satisfaz o tipo
            'url' => (string) MaterialUrl::normalize($data['url']), // @pest-mutate-ignore: RemoveStringCast
            'anonymous' => $this->boolean('anonymous'),
        ];
    }

    private function urlRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $url = is_string($value) ? MaterialUrl::normalize($value) : null;

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
            $editingId = $editing instanceof Material ? $editing->id : null;
            /** @var Discipline $discipline */
            $discipline = $editing instanceof Material ? $editing->discipline : $this->route('discipline');

            $taken = Material::where('discipline_id', $discipline->id)
                ->where('url', $url)
                ->whereNull('revises_id')
                ->when($editingId !== null, fn ($query) => $query->whereKeyNot($editingId))
                ->whereIn('status', [MaterialStatus::Pendente, MaterialStatus::Aprovado])
                ->exists();

            if ($taken) {
                $fail(self::DUPLICATE_URL);
            }
        };
    }
}
