# Configurações — plano de implementação

> **Para agentes:** use `superpowers:subagent-driven-development` ou `superpowers:executing-plans` para executar tarefa por tarefa. Os passos usam checkbox (`- [ ]`).

**Objetivo:** substituir o placeholder de `/configuracoes` por uma página com o e-mail somente leitura e a troca de senha, que encerra as outras sessões da conta.

**Arquitetura:** um endpoint autenticado (`PUT /api/v1/me/password`) com Form Request, Action e limitador próprio. A SPA ganha `features/settings/`, reaproveitando `TextField`, `FormAlert`, `useMe` e a guarda de saída do Perfil, que é extraída para um hook.

**Stack:** Laravel 13, Pest, Sanctum (cookie), React 19, React Hook Form, TanStack Query, openapi-fetch.

**Spec:** `docs/specs/2026-11-04-configuracoes-design.md`

## Restrições globais

- Branch `feat/configuracoes`. Commits em Conventional Commits, em português, terminando com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
- Não entram: "Esqueci minha senha", e-mail de aviso, tema, idioma, preferências e exclusão de conta.
- Regras da senha: mínimo de 8 caracteres e confirmação igual. Sem regra de composição. Nova senha igual à atual é aceita.
- Limite: 5 tentativas por minuto, por id do usuário.
- Não rodar formatadores no front. Depois de mudar a API, rodar `make api-types`.
- Os textos de tela são os da seção 5 da spec, e as mensagens de validação são as da seção 4.

**Mudança em relação à spec:** a regeneração da sessão atual destrói o id antigo (`regenerate(true)`). Sem isso, a linha antiga continuaria em `sessions` até expirar.

> **Correção (04/11/2026):** este plano dizia que um cookie roubado sobreviveria à troca. O teste de sessão real mostrou que o `AuthenticateSession` já o recusa pela divergência do hash da senha. O `regenerate(true)` é uma camada extra. A spec é corrigida na Tarefa 4.

## Foco de revisão

1. **Enter duas vezes ou duplo clique durante o envio:** só um PUT sai. Um segundo envio falharia, porque a senha atual já mudou. Testado na Tarefa 3.
2. **Senha com espaços nas pontas:** é preservada exatamente como digitada. Testado na Tarefa 1.
3. **Cookie antigo depois da troca:** deixa de valer, e a sessão atual continua fazendo novas alterações com o CSRF renovado. Testado na Tarefa 3.
4. **Sessão expirada com o formulário preenchido:** vai para o Login, sem perguntar "Descartar alterações?". Testado na Tarefa 3.
5. **E-mail longo a 320 px e recarregar `/configuracoes`:** sem rolagem horizontal, e a página volta a abrir normalmente. Testado na Tarefa 3.

## Mapa de arquivos

| Arquivo | Papel |
|---|---|
| `backend/app/Http/Requests/ChangePasswordRequest.php` | regras e mensagens |
| `backend/app/Actions/ChangePassword.php` | grava a senha e encerra as outras sessões |
| `backend/app/Http/Controllers/Api/V1/ChangePasswordController.php` | chama a Action e regenera a sessão |
| `backend/app/Providers/AppServiceProvider.php` | limitador `password` |
| `backend/app/Http/Resources/UserResource.php` | adiciona `email` |
| `backend/routes/api.php` | rota |
| `backend/tests/Feature/Auth/ChangePasswordTest.php` | testes da troca |
| `frontend/src/components/layout/useUnsavedChangesGuard.ts` | guarda de saída extraída |
| `frontend/src/components/TextField.tsx` | prop `visibilityLabel` |
| `frontend/src/features/settings/{hooks.ts,SettingsPage.tsx}` | página |
| `frontend/src/app/router.tsx` | rota; remove `placeholders` |
| `frontend/src/pages/UnderConstructionPage.tsx` | removido |

---

### Tarefa 1: API de troca de senha

**Arquivos:** os de `backend/` do mapa, mais `backend/tests/Feature/Auth/LoginTest.php` (o `assertExactJson` passa a incluir `email`), `frontend/src/lib/api/schema.d.ts` e o OpenAPI (gerados).

**Interfaces:**
- Produz: `PUT /api/v1/me/password` com `{current_password, password, password_confirmation}`, respondendo 204.
- Produz: `UserResource.email: string`.
- Produz: `ChangePassword::handle(User $user, string $password, string $currentSessionId): void`.

- [ ] **Passo 1: escrever os testes que falham em `ChangePasswordTest.php`**
  - `troca a senha`: 204; `Hash::check` aceita a nova e recusa a antiga.
  - `aceita nova senha igual à atual`: 204.
  - `preserva espaços nas pontas da senha`: nova senha `'  segredo novo  '`; `Hash::check` aceita a string exata e recusa a versão sem espaços.
  - `valida cada campo` (dataset com a tabela da seção 4 da spec): 422 com a mensagem exata em `current_password`, `password` ou `password_confirmation`; `Hash::check` continua aceitando a senha antiga.
  - `mostra todos os erros juntos`: senha atual errada e nova com 3 caracteres dão 422 nos dois campos.
  - `encerra só as outras sessões`: chama `ChangePassword::handle` com linhas em `sessions` para `atual` e `outra` do usuário e `alheia` de outro usuário. Depois disso, existem `atual` e `alheia`, e `outra` não existe.
  - `não encerra sessões quando a troca falha`: PUT com senha atual errada mantém a linha `outra`.
  - `limita a 5 tentativas por usuário`: a sexta responde 429 com mensagem começando por `Muitas tentativas. Tente novamente em`; outro usuário ainda recebe 204.
  - `exige sessão`: 401 sem login.
  - `expõe o e-mail só para a própria pessoa`: `GET /me` traz `email`; `GET /students/{public_code}` não traz.

- [ ] **Passo 2: ver falhar**

  Rodar `docker compose exec -T -u sail app php artisan test --filter=ChangePassword`. Esperado: falha, com 404 na rota ou classe inexistente.

- [ ] **Passo 3: implementar**
  - `ChangePasswordRequest`: `current_password` com `['bail', 'required', 'string', 'current_password:web']`, `password` com `['required', 'string', 'min:8']` e `password_confirmation` com `['required', 'same:password']`.
  - `ChangePassword::handle`: em `DB::transaction`, atualiza a senha e apaga de `sessions` onde `user_id` é o do usuário e `id` é diferente de `$currentSessionId`.
  - `ChangePasswordController`: chama a Action com `$request->session()->getId()`, depois `$request->session()->regenerate(true)`, e responde `noContent()`.
  - Limitador: `RateLimiter::for('password', ...)` com `Limit::perMinute(5)->by((string) $request->user()->id)` e `tooManyAttempts`.
  - Rota dentro de `auth:sanctum`: `Route::put('me/password', ChangePasswordController::class)->middleware('throttle:password')`.
  - `UserResource`: adicionar `'email' => $this->email`.

- [ ] **Passo 4: ver passar**

  Rodar `make test`, `make api-types` e `make lint`. Esperado: tudo verde, e o `schema.d.ts` com `/me/password` e `email`.

- [ ] **Passo 5: commit** `feat: adiciona a API de troca de senha que encerra as outras sessões`

### Tarefa 2: componentes compartilhados

**Arquivos:** `frontend/src/components/layout/useUnsavedChangesGuard.ts` (criar), `frontend/src/features/profile/ProfileForm.tsx`, `frontend/src/components/TextField.tsx`.

**Interfaces:**
- Produz: `useUnsavedChangesGuard(isDirty: boolean, isFreeExit?: (current: Location, next: Location) => boolean): { askDiscard: () => Promise<boolean>; dialogProps: { open: boolean; onContinue: () => void; onDiscard: () => void } }`.
- Produz: `TextField` com `visibilityLabel?: string`, padrão `'senha'`. O botão diz `Mostrar ${visibilityLabel}` ou `Ocultar ${visibilityLabel}`.

- [ ] **Passo 1: mover do `ProfileForm` para o hook** o estado `confirm`, `askDiscard`, o `useBlocker` e os efeitos de bloqueio, `setLeaveCheck` e `beforeunload`. O hook bloqueia quando `isDirty`, o destino não é `/login` e `isFreeExit` não libera a saída. O `ProfileForm` passa em `isFreeExit` as regras dele: `staysEditing` e `leaveEditing`. O comentário sobre `/login` vai junto.

- [ ] **Passo 2: adicionar `visibilityLabel` ao `TextField`.**

- [ ] **Passo 3: verificar que o Perfil não mudou**

  Rodar `make lint`. Depois, com um roteiro Playwright: editar o Perfil e trocar de página mostra o diálogo; "Continuar editando" fica e "Descartar alterações" sai; alternar `?editar=disciplinas` não pergunta; salvar não pergunta; o Login mantém "Mostrar senha".

- [ ] **Passo 4: commit** `refactor: extrai a guarda de saída do Perfil para um hook`

### Tarefa 3: página de Configurações

**Arquivos:** `frontend/src/features/settings/hooks.ts`, `frontend/src/features/settings/SettingsPage.tsx`, `frontend/src/app/router.tsx`; remover `frontend/src/pages/UnderConstructionPage.tsx`.

**Interfaces:**
- Consome: `PUT /me/password`, `User.email`, `useUnsavedChangesGuard`, `visibilityLabel`, `DiscardChangesDialog` de `features/profile`.
- Produz: `useChangePassword()`, mutação que chama `ensureCsrfCookie()` e `call(api.PUT('/me/password', { body }))`.
- Produz: `SettingsPage`, com export nomeado.

- [ ] **Passo 1: implementar a página**
  - Formulário com React Hook Form, `noValidate` e os textos da seção 5 da spec.
  - O erro da confirmação compara com o valor atual de `password`, com `getValues`.
  - O envio retorna sem fazer nada enquanto `isPending` for verdadeiro.
  - Sucesso: `reset()`, aviso verde e troca da `key` dos três `TextField`, para voltarem a ficar ocultos.
  - Erro em `current_password`: `resetField('current_password')`, depois `setError` com `shouldFocus`.
  - Outros 422: `applyFieldErrors`. 429: `FormAlert` vermelho com `error.message`. Demais falhas: `FormAlert` vermelho com o texto da spec.
  - E-mail com `[overflow-wrap:anywhere]`. Campo oculto `<input type="email" autoComplete="username" readOnly tabIndex={-1} aria-hidden className="sr-only">`.
  - Guarda: `useUnsavedChangesGuard(isDirty)`, com `DiscardChangesDialog` recebendo `dialogProps` e `description="As alterações feitas ainda não foram salvas."`, porque o texto padrão dele cita o perfil.

- [ ] **Passo 2: registrar a rota**, remover `placeholders` de `router.tsx` e apagar `UnderConstructionPage.tsx`.

- [ ] **Passo 3: rodar `make lint`.** Esperado: sem erros.

- [ ] **Passo 4: roteiro Playwright no scratchpad**, em 1440×900 e 390×844, com a largura de 320 px para a rolagem

  Usar uma conta criada só para o teste, apagada no fim. Conferir:
  - dois navegadores na mesma conta: o primeiro troca a senha e continua logado; o segundo recebe 401 e vai para o Login;
  - a senha nova entra e a antiga recebe "E-mail ou senha incorretos.";
  - o cookie anterior à troca, reenviado, recebe 401;
  - uma segunda troca na mesma aba funciona, sem erro de CSRF;
  - Enter duas vezes gera um só PUT;
  - validação vazia e confirmação diferente mostram as mensagens exatas;
  - senha atual errada limpa o campo e leva o foco até ele;
  - 429 (seis envios) e falha de rede (`route.abort`) mantêm os campos;
  - depois do sucesso, os campos ficam vazios e `type="password"`;
  - sair com campo preenchido mostra "Descartar alterações?";
  - apagar a sessão e enviar leva ao Login, sem o diálogo;
  - recarregar `/configuracoes` funciona;
  - e-mail longo a 320 px não cria rolagem horizontal;
  - os botões têm os rótulos "Mostrar senha atual", "Mostrar nova senha" e "Mostrar confirmação da nova senha".

  Conferir também as capturas de tela. Esperado: todas as asserções passam, e os dados de teste são removidos.

- [ ] **Passo 5: commit** `feat: adiciona a página de Configurações com troca de senha`

### Tarefa 4: documentação e verificação final

**Arquivos:** os da seção 7 da spec, mais `frontend/AGENTS.md` (retira a linha de `pages/`, adiciona `settings` e cita `useUnsavedChangesGuard`) e a própria spec (`regenerate(true)`, com o motivo).

- [ ] **Passo 1: atualizar os documentos** seguindo `docs/AGENTS.md`: nova seção 61 em `decisoes.md`; `> **Atualização (04/11/2026):**` em `pendencias.md` 2.5; itens marcados em `implementacao.md` §13; linha de Configurações em `status-mvp.md`; `roadmap.md`; `README.md`; `fluxos.md` §22; RF-59 em `requisitos.md`.

- [ ] **Passo 2: verificação final**

  Rodar `make test` e `make lint`. Esperado: tudo verde. Rodar também `grep -rn "UnderConstruction\|Em construção" frontend/src`. Esperado: nenhum resultado.

- [ ] **Passo 3: commit** `docs: registra a entrega de Configurações`
