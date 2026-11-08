<?php

namespace App\Http\Requests;

use App\Support\ProfileUrl;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    // shortcut: teto provisório até a definição de pendencias.md 2.7
    public const MAX_DISPLAY_NAME = 120;

    public const MAX_ABOUT = 5000;

    public const MAX_TAG = 255;

    public const MAX_TAGS = 50;

    public const TAG_FIELDS = ['interests', 'can_help_with', 'needs_help_with'];

    private const GITHUB_INVALID = 'Informe o endereço do seu perfil no GitHub, como https://github.com/usuario.';

    private const LINKEDIN_INVALID = 'Informe o endereço do seu perfil no LinkedIn, como https://www.linkedin.com/in/usuario.';

    protected function prepareForValidation(): void
    {
        // espaços nas pontas e texto vazio já chegam tratados pelos middlewares TrimStrings e ConvertEmptyStringsToNull
        $merge = [];

        foreach (self::TAG_FIELDS as $field) {
            $value = $this->input($field, []);
            $merge[$field] = is_array($value) ? self::normalizeTags($value) : $value;
        }

        $this->merge($merge);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'display_name' => ['required', 'string', 'max:'.self::MAX_DISPLAY_NAME],
            'discipline_ids' => ['required', 'array'],
            'discipline_ids.*' => ['integer', 'exists:disciplines,id'],
            'about' => ['nullable', 'string', 'max:'.self::MAX_ABOUT],
            'github_url' => ['nullable', self::linkRule(ProfileUrl::github(...), self::GITHUB_INVALID)],
            'linkedin_url' => ['nullable', self::linkRule(ProfileUrl::linkedin(...), self::LINKEDIN_INVALID)],
        ];

        foreach (self::TAG_FIELDS as $field) {
            $rules[$field] = ['array', 'max:'.self::MAX_TAGS];
            $rules["{$field}.*"] = ['string', 'max:'.self::MAX_TAG];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $messages = [
            'display_name.required' => 'Informe seu nome de exibição.',
            'display_name.string' => 'Informe seu nome de exibição.',
            'display_name.max' => 'Use no máximo 120 caracteres.',
            'discipline_ids.required' => 'Selecione pelo menos uma disciplina atual para completar seu perfil.',
            'discipline_ids.array' => 'Selecione pelo menos uma disciplina atual para completar seu perfil.',
            'discipline_ids.*.integer' => 'Selecione disciplinas da lista.',
            'discipline_ids.*.exists' => 'Selecione disciplinas da lista.',
            'about.string' => 'Use no máximo 5.000 caracteres.',
            'about.max' => 'Use no máximo 5.000 caracteres.',
        ];

        foreach (self::TAG_FIELDS as $field) {
            $messages["{$field}.array"] = 'Adicione no máximo 50 assuntos.';
            $messages["{$field}.max"] = 'Adicione no máximo 50 assuntos.';
            $messages["{$field}.*.string"] = 'Use no máximo 255 caracteres por assunto.';
            $messages["{$field}.*.max"] = 'Use no máximo 255 caracteres por assunto.';
        }

        return $messages;
    }

    /**
     * Dados validados com os links já na forma normalizada.
     *
     * @return array{display_name: string, discipline_ids: list<int>, about: ?string, interests: list<string>, can_help_with: list<string>, needs_help_with: list<string>, github_url: ?string, linkedin_url: ?string}
     */
    public function profileData(): array
    {
        /** @var array{display_name: string, discipline_ids: list<int>, about: ?string, interests: list<string>, can_help_with: list<string>, needs_help_with: list<string>, github_url: ?string, linkedin_url: ?string} $data */
        $data = $this->validated();
        $data['github_url'] = isset($data['github_url']) ? ProfileUrl::github($data['github_url']) : null;
        $data['linkedin_url'] = isset($data['linkedin_url']) ? ProfileUrl::linkedin($data['linkedin_url']) : null;
        $data['about'] ??= null;

        return $data;
    }

    /**
     * Remove vazios e repetidos (sem diferenciar maiúsculas), mantendo a primeira ocorrência.
     * Itens que não são texto ficam como estão para a validação recusá-los.
     *
     * @param  array<mixed>  $tags
     * @return list<mixed>
     */
    private static function normalizeTags(array $tags): array
    {
        $seen = [];
        $result = [];
        foreach ($tags as $tag) {
            if ($tag === null) {
                continue; // os middlewares TrimStrings e ConvertEmptyStringsToNull transformam a tag '  ' em null
            }
            if (! is_string($tag)) {
                $result[] = $tag;

                // parar aqui daria no mesmo: a tag que não é texto já reprova a validação
                continue; // @pest-mutate-ignore: ContinueToBreak
            }
            $key = mb_strtolower($tag);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true; // @pest-mutate-ignore: TrueToFalse
            $result[] = $tag;
        }

        return $result;
    }

    /** @param  Closure(string): ?string  $normalize */
    private static function linkRule(Closure $normalize, string $message): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($normalize, $message): void {
            if (! is_string($value) || $normalize($value) === null) {
                $fail($message);
            }
        };
    }
}
