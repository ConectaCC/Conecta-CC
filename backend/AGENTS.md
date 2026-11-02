# AGENTS.md — backend (API Laravel)

Leia antes o [`AGENTS.md`](../AGENTS.md) da raiz. Decisões de stack: `docs/stack-e-decisoes-tecnicas.md` (decisões 1, 2, 4 e 11).

## Stack

PHP 8.4, Laravel 13, PostgreSQL 17, Sanctum (sessão por cookie, mesma origem da SPA), Scramble (OpenAPI gerado do código), Pest, Pint e Larastan nível 6. Filament fica reservado para o painel `/admin`, que só o ADMINISTRADOR acessa.

## Estrutura e responsabilidades

| Pasta | Papel | Exemplo |
|---|---|---|
| `app/Http/Controllers/Api/V1/` | um controller **invocável** por endpoint | `UpdateProfileController` |
| `app/Http/Requests/` | validação, normalização da entrada e mensagens | `UpdateProfileRequest` |
| `app/Actions/` | uma classe por caso de uso, com o método `handle(...)` | `UpdateProfile`, `RegisterUser` |
| `app/Http/Resources/` | formato JSON de saída | `ProfileResource`, `DisciplineResource` |
| `app/Models/` | Eloquent, casts, relações e accessors | `Discipline::slug` |
| `app/Support/` | helpers puros, sem HTTP | `ProfileUrl`, `DisciplineCatalog` |
| `app/Rules/` | regras de validação próprias | `InstitutionalEmail` |
| `app/Enums/` | enums com permissões explícitas | `Role::canModerate()` |
| `app/Console/Commands/` | comandos artisan (`conectacc:*`) | `PromoteUser` |
| `routes/api.php` | rotas em `Route::prefix('v1')`, protegidas por `auth:sanctum` | — |

**Padrão de endpoint:** o controller é fino. Ele recebe o Form Request, chama a Action e devolve o Resource, sem regra de negócio:

```php
final class UpdateProfileController
{
    public function __invoke(UpdateProfileRequest $request, UpdateProfile $updateProfile): ProfileResource
    {
        return new ProfileResource($updateProfile->handle($request->user(), $request->profileData())->load('disciplines'));
    }
}
```

- As classes são `final`.
- Uma Action que grava em mais de uma tabela usa `DB::transaction`.
- Uma Action recebe dados tipados (`@param array{...}`), nunca o `Request`.
- Uma leitura simples pode ficar no controller, sem Action. Exemplo: `ListDisciplinesController` devolve o catálogo ordenado.
- `JsonResource::withoutWrapping()` está ligado, então as respostas não têm `data`.

## Contrato da API (OpenAPI)

- O Scramble gera o OpenAPI a partir dos tipos. Todo Resource declara o formato exato em `/** @return array{...} */`, com `string|null` quando aceita nulo. Todo Request tem regras completas.
- Depois de qualquer mudança na API, rode `make api-types` e commite `frontend/openapi.json` e `frontend/src/lib/api/schema.d.ts`. O CI falha se estiverem desatualizados.
- Para adicionar um campo calculado ao JSON, prefira um accessor no model (`Attribute::get`) a uma coluna nova, quando não for preciso consultar o banco por ele.

## Validação e mensagens

- As mensagens ficam em português e são **exatamente** as da spec. Exemplos: "Informe seu nome de exibição.", "E-mail ou senha incorretos.".
- Normalize a entrada em `prepareForValidation()`: `trim`, e-mail em minúsculas, links de perfil no formato canônico (`https://github.com/usuario`).
- **Payload malformado** (string no lugar de array, array no lugar de string) devolve **422 com mensagem, nunca 500**.
- **Limites de texto provisórios** ficam em constantes `MAX_*` no Request, com `// shortcut: teto provisório até a definição de pendencias.md 2.7`.
- **Links externos** aceitam só `http`/`https`, validados no servidor. GitHub e LinkedIn aceitam só o endereço do perfil da pessoa (`decisoes.md` 53.3).
- **Limite de tentativas:** fica em `AppServiceProvider` (`RateLimiter::for`). Login e Cadastro aceitam 5 por minuto. A resposta 429 diz "Muitas tentativas. Tente novamente em X segundos."

## Autorização

- Use Gates e Policies, nunca só a interface. Exemplo: `Gate::define('access-moderation', ...)` em `AppServiceProvider`.
- Papéis: `Role` (ALUNO, MODERADOR, ADMINISTRADOR) **sem herança automática**. Cada permissão é um método explícito no enum (`canModerate()`).
- O MODERADOR não modera o próprio conteúdo, nem vê o próprio envio na fila. O ADMINISTRADOR pode moderar o próprio conteúdo (`decisoes.md` §51.2). Duas decisões de Moderação nunca se sobrescrevem (`implementacao.md` §15). Garanta tudo isso no backend (Policy e `UPDATE ... WHERE status = 'PENDENTE'`).
- Conflitos de regra respondem 409 com `{message, code}` via `App\Support\Conflict::abort()`. O front decide pelo `code`, nunca pelo texto.

## Banco de dados

- Use PostgreSQL de propósito: `CHECK` constraints para regras (papel, semestre de 1 a 8), índices parciais e `unaccent` quando houver busca no banco.
- **Nunca edite uma migration já mergeada**, porque ela já rodou em produção. Crie uma nova.
- **Migrations de dados** (como a carga do catálogo) chamam código da aplicação (`DisciplineCatalog::sync`). Cuidado: se esse código passar a usar uma coluna criada por uma migration posterior, o `migrate:fresh` quebra. Foi por isso que o `slug` das disciplinas virou accessor.
- **Catálogo de disciplinas:** vem de `resources/catalog/matriz-2023.json`, com upsert pelo nome. São 49 obrigatórias da Matriz 2023, sem eletivas. Para atualizar, veja `docs/operacao.md`.
- Seeders são só para dev. As contas de teste estão em `database/seeders/DatabaseSeeder.php`.

## Testes (Pest)

- Rode com `make test`. Os testes usam **PostgreSQL real**, local e no CI, porque parte das regras vive no banco.
- Os Feature tests ficam por área em `tests/Feature/<Area>/` (por exemplo, `Auth/`, `Profile/`). Unit tests ficam em `tests/Unit/`, para helpers puros como `ProfileUrlTest`.
- Os nomes ficam em português, com `it('...')`, e descrevem o comportamento. Exemplo: `it('exige login para ver o catálogo')`.
- Todo endpoint autenticado tem um teste de **401 sem sessão**. Todo endpoint com papel tem teste de **acesso negado**.
- Use TDD: escreva o teste, veja falhar e só depois implemente. Teste os valores exatos da spec: mensagens, limites, URLs.
- Para cada entrada que a spec não cobre mas que vai acontecer (variações de URL, maiúsculas, espaços, payload malformado, requisição duplicada), crie um teste.

## Qualidade

- `make lint` roda o Pint (estilo Laravel) e o Larastan nível 6. Arrays recebem tipo em docblock (`list<int>`, `array{...}`) e accessors recebem `@property-read`.
- Comandos artisan recusam entrada inválida com mensagem clara e código de saída diferente de zero. Em produção, rodam pelo workflow do GitHub Actions.
