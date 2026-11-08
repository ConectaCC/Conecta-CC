# AGENTS.md — backend (API Laravel)

Leia antes o [`AGENTS.md`](../AGENTS.md) da raiz. Decisões de stack: `docs/stack-e-decisoes-tecnicas.md` (decisões 1, 2, 4 e 11).

## Stack

PHP 8.4, Laravel 13, PostgreSQL 17, Sanctum (sessão por cookie, mesma origem da SPA), Scramble (OpenAPI gerado do código), Pest, Pint e Larastan nível 10. Filament fica reservado para o painel `/admin`, que só o ADMINISTRADOR acessa.

## Estrutura e responsabilidades

| Pasta | Papel | Exemplo |
|---|---|---|
| `app/Http/Controllers/Api/V1/` | um controller **invocável** por endpoint | `UpdateProfileController` |
| `app/Http/Requests/` | validação, normalização da entrada e mensagens | `UpdateProfileRequest` |
| `app/Actions/` | uma classe por caso de uso, com o método `handle(...)` | `UpdateProfile`, `RegisterUser` |
| `app/Http/Resources/` | formato JSON de saída | `ProfileResource`, `DisciplineResource` |
| `app/Models/` | Eloquent, casts, relações e accessors | `Discipline::slug` |
| `app/Support/` | helpers puros e regras reaproveitadas | `ProfileUrl`, `MaterialUrl`, `DisciplineCatalog`, `Conflict`, `MaterialSubmissionLimit` |
| `app/Notifications/` | notificações nativas do Laravel (canal `database`) | `MaterialDecided` |
| `app/Rules/` | regras de validação próprias | `InstitutionalEmail` |
| `app/Enums/` | enums com permissões explícitas | `Role::canModerate()` |
| `app/Console/Commands/` | comandos artisan (`conectacc:*`) | `PromoteUser` |
| `app/Actions/RecordAuditEvent.php` | toda Action que muda dados grava o evento de auditoria na mesma transação (`decisoes.md` 66) | `RecordAuditEvent` |
| `app/Filament/`, `app/Providers/Filament/`, `public/css/conectacc-admin.css` | painel `/admin`. A regra fica em uma Action, e a tela só a chama. O CSS aproxima o painel do app | `UserResource`, `MaterialResource` |
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
- **Armadilhas do Scramble:**
  - comentário dentro do array do Resource vira descrição pública na API: nota interna fica fora do array;
  - um valor tirado de array misto pode ser documentado com o tipo errado: converta (`(bool)`) antes;
  - paginação a partir de relação (`$user->materials()`) pode ser documentada como array simples: parta de `Model::query()`;
  - parâmetro de query lido implicitamente, como `cursor`, precisa de `#[QueryParameter]`.
- **Parâmetros de rota:** todo parâmetro tem formato restrito em `Route::pattern` (`AppServiceProvider`), para que um valor inválido dê 404, e não 500 no PostgreSQL.

## Validação e mensagens

- As mensagens ficam em português e são **exatamente** as da spec. Exemplos: "Informe seu nome de exibição.", "E-mail ou senha incorretos.".
- Os middlewares globais `TrimStrings` e `ConvertEmptyStringsToNull` já tiram os espaços das pontas e transformam texto vazio em `null`. Não repita isso no Request.
- Normalize em `prepareForValidation()` só o resto: e-mail em minúsculas, links de perfil no formato canônico (`https://github.com/usuario`).
- Valor que não é texto (lista no lugar de texto) recebe a mensagem padrão de `lang/pt_BR/validation.php`, em português.
- **Payload malformado** (string no lugar de array, array no lugar de string) devolve **422 com mensagem, nunca 500**.
- **Limites de texto provisórios** ficam em constantes `MAX_*` no Request, com `// shortcut: teto provisório até a definição de pendencias.md 2.7`.
- **Links externos** aceitam só `http`/`https`, validados no servidor. GitHub e LinkedIn aceitam só o endereço do perfil da pessoa (`decisoes.md` 53.3).
- **Conteúdo do estudante:** o limite de 10 envios ou edições por hora está em `MaterialSubmissionLimit` e conta só o que deu certo. Bytes nulos são removidos da entrada pelo middleware `StripNullBytes`.
- **Limite de tentativas:** fica em `AppServiceProvider` (`RateLimiter::for`). Login, Cadastro e troca de senha aceitam 5 por minuto. A resposta 429 diz "Muitas tentativas. Tente novamente em X segundos."

## Autorização

- Use Gates e Policies, nunca só a interface. Exemplo: `Gate::define('access-moderation', ...)` em `AppServiceProvider`.
- Papéis: `Role` (ALUNO, MODERADOR, ADMINISTRADOR) **sem herança automática**. Cada permissão é um método explícito no enum (`canModerate()`).
- O MODERADOR não modera o próprio conteúdo, nem vê o próprio envio na fila. O ADMINISTRADOR pode moderar o próprio conteúdo (`decisoes.md` §51.2). Duas decisões de Moderação nunca se sobrescrevem (`implementacao.md` §15). Garanta tudo isso no backend (Policy e `UPDATE ... WHERE status = 'PENDENTE'`).
- Conflitos de regra respondem 409 com `{message, code}` via `App\Support\Conflict::abort()`. O front decide pelo `code`, nunca pelo texto.

## Privacidade

- **Resource público é lista fechada de campos.** E-mail, nome completo, papel e histórico privado nunca entram no Perfil público (`StudentProfileResource`).
- **Material anônimo:** `author_name` e `author_code` vão nulos na área pública, e o MODERADOR não recebe o nome. Teste com `assertDontSee`.
- **URL do Perfil público:** usa `users.public_code`, aleatório, nunca o id interno. O código não substitui autorização.

## Segurança

- **Cabeçalhos:** `SecurityHeaders` (global) manda nosniff, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, COOP e, por HTTPS, o HSTS.
- **CSP da SPA:** fica na rota da SPA (`routes/web.php`) e só aceita scripts do próprio site. Um recurso externo novo (script, fonte, imagem, chamada de API) precisa entrar na política, ou o navegador o bloqueia. Confira com o build de produção servido pelo Laravel.
- **CORS fechado:** a SPA é da mesma origem. Não abra o CORS para outro site sem uma decisão.
- **Proxy:** confiamos no `X-Forwarded-For`, `-Proto` e `-Port` do Render, nunca no `X-Forwarded-Host`.
- **Limites:** além dos limites de Login, Cadastro, senha e envios, a API inteira tem um teto de 120 pedidos por minuto por pessoa (ou por IP, sem sessão).
- **Dependências:** `composer audit` e `npm audit --omit=dev` sem avisos. O Dependabot abre PRs semanais.

## Banco de dados

- Use PostgreSQL de propósito: `CHECK` constraints para regras (papel, semestre de 1 a 8), índices parciais e `unaccent` quando houver busca no banco.
- **Nunca edite uma migration já mergeada**, porque ela já rodou em produção. Crie uma nova.
- **Migrations de dados** (como a carga do catálogo) chamam código da aplicação (`DisciplineCatalog::sync`). Cuidado: se esse código passar a usar uma coluna criada por uma migration posterior, o `migrate:fresh` quebra. Foi por isso que o `slug` das disciplinas virou accessor.
- **Valor gerado pelo banco:** colunas como `public_code` não aparecem no model logo depois do `create()`: use `refresh()`. A coluna `data` de `notifications` é texto, então não aceita consulta JSON.
- **Catálogo de disciplinas:** vem de `resources/catalog/matriz-2023.json`, com upsert pelo nome. São 49 obrigatórias da Matriz 2023, sem eletivas. Para atualizar, veja `docs/operacao.md`.
- Seeders são só para dev. As contas de teste estão em `database/seeders/DatabaseSeeder.php`.

## Testes (Pest)

- Rode com `make test`. Os testes usam **PostgreSQL real**, local e no CI, porque parte das regras vive no banco.
- Os Feature tests ficam por área em `tests/Feature/<Area>/` (por exemplo, `Auth/`, `Profile/`). Unit tests ficam em `tests/Unit/`, para helpers puros como `ProfileUrlTest`.
- Os nomes ficam em português, com `it('...')`, e descrevem o comportamento. Exemplo: `it('exige login para ver o catálogo')`.
- Todo endpoint autenticado tem um teste de **401 sem sessão**. Todo endpoint com papel tem teste de **acesso negado**.
- Use TDD: escreva o teste, veja falhar e só depois implemente. Teste os valores exatos da spec: mensagens, limites, URLs.
- Para cada entrada que a spec não cobre mas que vai acontecer (variações de URL, maiúsculas, espaços, payload malformado, requisição duplicada), crie um teste.
- **Dois usuários no mesmo teste:** a sessão guarda o hash da senha (`AuthenticateSession`) e o guard do Sanctum guarda o usuário. Quando o resultado depende de quem é o usuário atual, rode `$this->flushSession()` e `app('auth')->forgetGuards()` antes de trocar.

- **Formato das respostas:** `tests/Feature/ResponseShapesTest.php` confere, campo a campo, os itens que a SPA consome. Campo novo num Resource entra lá.

## Qualidade

- `make lint` roda o Pint (estilo Laravel) e o Larastan nível 10. Arrays recebem tipo em docblock (`list<int>`, `array{...}`) e accessors recebem `@property-read`. Valor `mixed` (de `config()`, `env()`, `input()`) é conferido com `is_string` e afins, ou lido com `config()->string()` e `$request->string()`.
- **Antes do PR, rode `make mutate`.** Ele exige 100% de cobertura de linhas e nota de mutação de pelo menos 90% nas classes de `app/` alteradas em relação à `origin/main`. O CI roda o mesmo.
- **Mutante que sobreviveu:** primeiro veja se o código é redundante e pode sair (validação repetida por outra regra ou por índice do banco, cast que o model já faz). Se é lacuna, escreva o teste. Só se a mudança não altera o comportamento, marque a linha:

  ```php
  // a rota exige sessão: o ?-> só satisfaz o tipo
  'is_mine' => $request->user()?->id === $this->user_id, // @pest-mutate-ignore: RemoveNullSafeOperator
  ```

  O motivo fica na linha de cima. Depois dos dois-pontos vão só os nomes dos mutadores, separados por vírgula: qualquer texto a mais vira nome de mutador e a marca deixa de valer.
- **No `/admin`,** o teste cobre a regra (consulta, filtro, o que o detalhe mostra, permissão). O que é só layout (`columns(1)`, placeholder de select) fica marcado.
- **`preventLazyLoading`** está ligado fora de produção. Um N+1 em lista vira erro no teste. O Filament já carrega junto as relações das colunas.
- Comandos artisan recusam entrada inválida com mensagem clara e código de saída diferente de zero. Em produção, rodam pelo workflow do GitHub Actions.
