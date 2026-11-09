# Estudos parte 2, etapa 2a: Materiais e Moderação — plano de implementação

> **Execução:** tarefa por tarefa. Os passos usam checkbox (`- [ ]`) para acompanhamento.

**Objetivo:** o estudante compartilha um material (identificado ou anônimo) numa disciplina, MODERADOR e ADMINISTRADOR aprovam ou rejeitam em `/moderacao`, e os materiais aprovados aparecem na página da disciplina com "Mostrar mais".

**Arquitetura:**

- O backend ganha:
  - a tabela `materials`, com o status de moderação nela mesma (spec §4.1);
  - Actions para criar, aprovar e rejeitar;
  - uma Policy;
  - dois Resources: um público e um da moderação, que esconde do MODERADOR o autor de um material anônimo.
- O front ganha:
  - a lista, o card e o diálogo de envio dentro da página da disciplina;
  - a página `/moderacao`, no lugar do placeholder, com lista, painel (`Sheet`) e confirmações (`Dialog`).
- Os Termos passam à versão 0.2.

**Stack:** a de sempre (Laravel 13, Pest, Scramble, PostgreSQL 17, React 19, TanStack Query, React Hook Form, openapi-fetch, shadcn/ui, Tailwind v4).

**Spec:** `docs/specs/2026-11-02-estudos-parte-2-design.md`. Esta etapa cobre as seções 2a da spec: §3, §4.1, §5.1 a §5.5, §6.1, §6.2 e §7. Leia a spec antes de cada tarefa. Siga também `AGENTS.md`, `backend/AGENTS.md` e `frontend/AGENTS.md`.

## Restrições globais

- **Git:**
  - branch `feat/materiais` (já criada, com a spec commitada);
  - Conventional Commits em português;
  - sem push.
- **Fora desta etapa:**
  - Meus conteúdos, Editar, Excluir, Cancelar e alteração;
  - Notificações (o sino não muda);
  - filtro por tipo na fila;
  - contador de materiais no card da disciplina;
  - link do nome do autor.
  - Exceção: as colunas `revises_id` e `deleted_at` já entram na migration da etapa 2a, para a 2b não precisar de outra.
- **Limites** (constantes em `StoreMaterialRequest`): `MAX_TITLE = 200`, `MAX_DESCRIPTION = 2000`, `MAX_URL = 2048` e `MAX_REASON = 500` (este em `RejectMaterialRequest`).
- **Throttle `materials`:** `Limit::perHour(10)->by(user id)`, com a mesma resposta 429 de `tooManyAttempts` ("Muitas tentativas. Tente novamente em X segundos.").
- **Mensagens da API, exatas:**
  - validação:
    - "Informe um título para o material."
    - "Informe o link do material."
    - "Informe um link válido."
    - "Esse link já foi compartilhado nesta disciplina."
    - "Use no máximo 200 caracteres."
    - "Use no máximo 2.000 caracteres."
    - "Use no máximo 2.048 caracteres."
  - rejeição:
    - "Informe o motivo da rejeição."
    - "Use no máximo 500 caracteres."
  - decisão (409): "Este conteúdo já foi analisado."
- **Textos do front, exatos:**
  - **Página da disciplina:**
    - "Materiais compartilhados" e "Mais recentes primeiro.";
    - o botão "Compartilhar material" e o link "Mostrar mais";
    - "Carregando materiais…";
    - erro: "Não foi possível carregar os materiais agora." e "Tentar novamente";
    - vazio: "Ainda não há materiais compartilhados" e "Se você conhece um material que pode ajudar outros estudantes, compartilhe com a comunidade.";
    - autoria: "Compartilhado por <nome> · <há X>" e "Compartilhado pela comunidade · <há X>";
    - o link "Acessar material" e o sr-only "(abre em nova guia, <domínio>)".
  - **Diálogo de envio:**
    - título "Compartilhar material";
    - "Campos marcados com * são obrigatórios.";
    - campos "Disciplina", "Título *", "Descrição" e "Link *";
    - ajudas "Explique brevemente o que o material contém." e "Use um link que outros estudantes consigam acessar.";
    - o checkbox "Compartilhar anonimamente", com a ajuda "Outros estudantes não verão seu nome. A administração do ConectaCC continua sabendo quem enviou.";
    - o aviso "O material será analisado pela moderação antes de aparecer na disciplina.";
    - os botões "Cancelar" e "Enviar para análise" ("Enviando…" durante o envio);
    - sucesso: "Material enviado para análise", "O material aparecerá na disciplina depois de aprovado." e "Voltar para a disciplina";
    - falha: "Não foi possível enviar o material. Tente novamente.";
    - descarte: "Descartar alterações?", "As informações deste material ainda não foram enviadas.", "Continuar preenchendo" e "Descartar alterações".
  - **Moderação:**
    - cabeçalho: "Moderação" e "Analise conteúdos enviados pela comunidade antes da publicação.";
    - contador: "N conteúdos pendentes" ou "1 conteúdo pendente", e "Mais antigos primeiro.";
    - item: "Tipo: Material", "Status: Pendente", "Seu envio", "Enviado por <nome ou Comunidade> · <há X> · Disciplina: <nome>" e o botão "Analisar";
    - painel:
      - título "Analisar conteúdo";
      - "Autor", "Comunidade", "Enviado anonimamente" e "Visível apenas para a moderação.";
      - "Enviado em dd/mm/aaaa às hh:mm (há X)";
      - "Disciplina", "Descrição", "Link enviado" e "Abrir link enviado";
      - "Se aprovado, aparecerá em Estudos › <disciplina>";
      - os botões "Rejeitar" e "Aprovar";
    - confirmação de aprovação: "Aprovar conteúdo?", "Depois da aprovação, este conteúdo ficará disponível na área correspondente do ConectaCC." e os botões "Cancelar" e "Aprovar conteúdo" ("Aprovando…");
    - confirmação de rejeição: "Rejeitar conteúdo?", o campo "Motivo da rejeição *", a ajuda "O estudante verá o motivo em Meus conteúdos." e os botões "Cancelar" e "Rejeitar conteúdo" ("Rejeitando…");
    - resultados:
      - aprovado: "Conteúdo aprovado com sucesso." e "O material passa a aparecer em Estudos › <disciplina>.";
      - rejeitado: "Conteúdo rejeitado.";
      - 409: "Este conteúdo já foi analisado", "O status deste conteúdo foi atualizado por outra ação. Atualize a fila para continuar." e "Atualizar fila";
      - falha: "Não foi possível aprovar o conteúdo. Tente novamente." e "Não foi possível rejeitar o conteúdo. Tente novamente.";
    - fila vazia: "Tudo certo por aqui" e "Não há conteúdos aguardando análise no momento.";
    - "Carregando conteúdos pendentes…";
    - erro: "Não foi possível carregar os conteúdos pendentes agora.".
- **Front:**
  - não há runner de testes, então a prova é `make lint` mais um roteiro Playwright fora do repositório (`<scratchpad>/pw/`, que reaproveita `login.js`);
  - não rodar Prettier;
  - não rodar `make fresh`;
  - antes de roteiros com muitos logins seguidos, rodar `docker compose exec -u sail app php artisan cache:clear`.

## Foco de revisão

1. **O mesmo link escrito de jeitos diferentes** (`site.com/x`, `  https://site.com/x  `, `https://site.com/x`) na mesma disciplina → os três ficam iguais depois de normalizados, então o segundo e o terceiro são recusados como repetidos (Tarefa 2).
2. **Duplo clique em "Enviar para análise"** → um só material criado. O botão fica desabilitado durante o envio (Tarefa 5). Se duas requisições passarem mesmo assim, o índice parcial transforma a segunda em 422, nunca em 500 (Tarefa 2).
3. **Dois moderadores decidem o mesmo item ao mesmo tempo** → o segundo recebe 409 e o status e o motivo do primeiro ficam como estavam (Tarefa 3).
4. **MODERADOR abre um material anônimo** → o nome do autor não aparece em nenhum lugar do JSON, nem em objeto aninhado, nem na fila, nem no detalhe (Tarefa 3).
5. **Material com título de 200 caracteres sem espaço ou link de 2.048 caracteres, num celular de 360px** → o texto quebra e não há rolagem horizontal, nem no card nem no painel da Moderação (Tarefas 5, 6 e 7).

---

## Mapa de arquivos

```
backend/
  app/Enums/MaterialStatus.php
  app/Models/Material.php                                   (+ Discipline::materials, User::materials)
  app/Support/MaterialUrl.php                               normaliza e valida o link
  app/Policies/MaterialPolicy.php
  app/Actions/{CreateMaterial,ApproveMaterial,RejectMaterial}.php
  app/Http/Requests/{StoreMaterialRequest,RejectMaterialRequest}.php
  app/Http/Resources/{MaterialResource,ModerationMaterialResource}.php
  app/Http/Controllers/Api/V1/{ListDisciplineMaterials,StoreMaterial,ListModerationMaterials,ApproveMaterial,RejectMaterial}Controller.php
  app/Providers/AppServiceProvider.php                      throttle `materials`
  routes/api.php
  database/migrations/2026_11_02_000001_create_materials_table.php
  database/factories/MaterialFactory.php
  database/seeders/{MaterialSeeder,DatabaseSeeder}.php
  resources/terms/0.2.json, config/conectacc.php, .env.example
  tests/Unit/MaterialUrlTest.php
  tests/Feature/Materials/{MaterialSchema,ShareMaterial,ListMaterials,Moderation}Test.php
  tests/Feature/TermsTest.php
render.yaml
frontend/src/
  lib/format.ts                                             timeAgo, formatDateTime, hostOf
  features/materials/{hooks.ts,MaterialsSection.tsx,MaterialCard.tsx,ShareMaterialDialog.tsx}
  features/moderation/{hooks.ts,ModerationPage.tsx,ReviewSheet.tsx,DecisionDialog.tsx}
  features/studies/DisciplinePage.tsx                       usa MaterialsSection e hostOf
  features/profile/DiscardChangesDialog.tsx                 prop `description` (padrão = texto atual)
  app/router.tsx                                            /moderacao → ModerationPage
docs/  decisoes.md, requisitos.md, pendencias.md, implementacao.md, status-mvp.md, operacao.md, README.md
```

---

### Tarefa 1: Tabela, model e normalização do link

**Arquivos:**
- Criar:
  - `MaterialStatus`, `Material`, `MaterialUrl`;
  - a migration;
  - `MaterialFactory`, `MaterialSeeder`;
  - os testes `tests/Unit/MaterialUrlTest.php` e `tests/Feature/Materials/MaterialSchemaTest.php`.
- Modificar: `Discipline.php` e `User.php` (relação `materials()`), `DatabaseSeeder.php` (chama `MaterialSeeder`).

**Interfaces:**
- **Produz:**
  - `enum MaterialStatus: string` com `Pendente = 'PENDENTE'`, `Aprovado = 'APROVADO'`, `Rejeitado = 'REJEITADO'` e `Cancelado = 'CANCELADO'`.
  - `Material` (com `SoftDeletes`):
    - fillable `title`, `description`, `url` e `anonymous`;
    - casts: `anonymous` bool, `status` `MaterialStatus`, `published_at` e `decided_at` datetime;
    - relações `discipline()`, `author()` (belongsTo `User`, `user_id`), `decider()` (`decided_by`) e `revisionOf()` (`revises_id`);
    - accessor `code`, com `sprintf('MAT-%05d', $id)` e `@property-read string $code`.
  - `MaterialUrl::normalize(string $input): ?string`. Recebe o texto digitado e devolve o link normalizado, ou `null` se o link for inválido. Regras:
    - aplica `trim`;
    - recusa texto com espaço interno;
    - sem `scheme://`, completa com `https://` só se o texto parecer um domínio (`^[^\s/]+\.[^\s]+$`);
    - aceita só `http` e `https`;
    - o host precisa ter pelo menos um ponto;
    - passa o host para minúsculas e mantém caminho, query e fragmento como vieram.
  - `MaterialFactory`, com os estados `approved()`, `rejected(string $reason)` e `anonymous()`.

- [ ] **Passo 1: Testes que falham.**
  - **`MaterialUrlTest`:**
    - `site.com/x` vira `https://site.com/x`;
    - `  https://Site.COM/A?b=1#c ` vira `https://site.com/A?b=1#c`;
    - `http://site.com` continua igual;
    - `javascript:alert(1)`, `ftp://site.com`, `https://site .com`, `localhost` e o texto vazio viram `null`.
  - **`MaterialSchemaTest`** (gravando com `DB::table` ou com a factory):
    - o banco recusa o status `'X'` (`QueryException`);
    - o banco recusa `REJEITADO` sem `rejection_reason`, e `APROVADO` com `rejection_reason`;
    - o banco recusa dois materiais `PENDENTE` ou `APROVADO` com o mesmo `(discipline_id, url)` (`UniqueConstraintViolationException`);
    - o banco aceita a mesma URL quando o primeiro está `REJEITADO`, `CANCELADO` ou com soft delete, ou quando a disciplina é outra;
    - `Material::factory()->create()->code` casa com `/^MAT-\d{5}$/`.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -u sail app vendor/bin/pest tests/Unit/MaterialUrlTest.php tests/Feature/Materials`. Resultado esperado: FAIL (as classes e a tabela não existem).

- [ ] **Passo 3: Implementar.**
  - **Migration `2026_11_02_000001_create_materials_table`:** as colunas da spec §4.1.
  - **`CHECK` e o índice parcial** via `DB::statement`, no mesmo padrão de `create_disciplines_tables`:
    - `materials_status_check`: `status IN ('PENDENTE','APROVADO','REJEITADO','CANCELADO')`;
    - `materials_rejection_reason_check`: `(status = 'REJEITADO') = (rejection_reason IS NOT NULL)`;
    - `CREATE UNIQUE INDEX materials_discipline_url_active ON materials (discipline_id, url) WHERE status IN ('PENDENTE','APROVADO') AND deleted_at IS NULL AND revises_id IS NULL`.
  - **Índices comuns:** `(status, created_at)` e `(discipline_id, status, published_at)`.
  - **`MaterialSeeder`** (só fora de produção; pode ser rodado sozinho com `php artisan db:seed --class=MaterialSeeder`). Usa `firstOrCreate` por `(discipline_id, url)` e cria:
    - 3 aprovados em "Estrutura de Dados III": um do `aluno@`, um anônimo do `aluno@` e um do `moderador2@`;
    - 1 pendente do `aluno@` em "Algoritmos I";
    - 1 pendente do `moderador@` em "Sistemas Distribuídos I".
    - Os links são `https://example.com/...`.

- [ ] **Passo 4: Rodar e ver passar.** Primeiro o comando do Passo 2, depois `make test` e `make lint`. Resultado esperado: tudo PASS.

- [ ] **Passo 5: Commit** — `feat: adiciona tabela de materiais com status de moderação`.

---

### Tarefa 2: API de envio e lista de materiais

**Arquivos:**
- Criar:
  - `StoreMaterialRequest`;
  - `CreateMaterial`;
  - `MaterialResource`;
  - `ListDisciplineMaterialsController` e `StoreMaterialController`;
  - os testes `tests/Feature/Materials/{ShareMaterial,ListMaterials}Test.php`.
- Modificar: `AppServiceProvider.php` (throttle `materials`) e `routes/api.php`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Consome:** `Material`, `MaterialStatus`, `MaterialUrl::normalize` (Tarefa 1).
- **Produz:**
  - **Rotas** (dentro de `auth:sanctum`):
    - `GET /api/v1/disciplines/{discipline}/materials`;
    - `POST /api/v1/disciplines/{discipline}/materials`, com `throttle:materials`.
  - **`StoreMaterialRequest`:**
    - `materialData(): array{title: string, description: ?string, url: string, anonymous: bool}`;
    - o `url` já vem normalizado;
    - a checagem de link repetido ignora material com `revises_id` (uma alteração não conta) e aceita um `except` opcional, para a 2b.
  - **`CreateMaterial::handle(User $author, Discipline $discipline, array $data): Material`:** cria o material como `PENDENTE`. Uma `UniqueConstraintViolationException` vira `ValidationException::withMessages(['url' => 'Esse link já foi compartilhado nesta disciplina.'])`.
  - **JSON de `MaterialResource`:**
    - `{id, code, title, description: ?string, url, author_name: ?string, published_at: string, is_mine: bool}`;
    - `author_name` é `null` quando `anonymous`;
    - `is_mine` compara o autor com `$request->user()`.
  - **A lista:**
    - usa `cursorPaginate(20)`, ordenada por `published_at desc, id desc`;
    - só traz materiais `APROVADO`, sem soft delete, com `revises_id` nulo e com `with('author')`;
    - o front lê `meta.next_cursor`.

- [ ] **Passo 1: Testes que falham.**
  - **`ShareMaterialTest`:**
    - o envio cria um material `PENDENTE` com o `user_id` do autor e devolve 201 com o `code`;
    - `anonymous: true` é gravado;
    - os 7 casos de mensagem das Restrições globais (os textos exatos em `errors.*`);
    - o mesmo link escrito de jeitos diferentes (`site.com/x`, `  https://site.com/x  `) conta como repetido, em pendente e em aprovado;
    - o mesmo link é aceito depois de rejeitado, cancelado ou excluído, e em outra disciplina;
    - payload malformado (`title` como array, `anonymous` como `"talvez"`, `url` como número) devolve 422;
    - o 11º envio na mesma hora recebe 429;
    - sem sessão, a resposta é 401.
  - **`ListMaterialsTest`:**
    - a lista só traz aprovados (pendente, rejeitado, com soft delete e com `revises_id` ficam de fora);
    - a ordem é por `published_at` decrescente;
    - a primeira página tem 20 itens e `meta.next_cursor` não é nulo, e a segunda página traz o resto;
    - um material anônimo vem com `author_name: null`, e o JSON não contém o nome de exibição do autor (`assertDontSee`);
    - `is_mine` é verdadeiro só para o autor;
    - sem sessão, a resposta é 401.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -u sail app vendor/bin/pest tests/Feature/Materials`. Resultado esperado: os testes novos falham com 404 (a rota não existe).

- [ ] **Passo 3: Implementar.** Seguir o padrão do endpoint de Perfil: um controller fino que recebe o Request, chama a Action e devolve o Resource.

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde, e `schema.d.ts` passa a ter `MaterialResource`.

- [ ] **Passo 5: Commit** — `feat: adiciona envio e lista de materiais por disciplina`.

---

### Tarefa 3: API de Moderação

**Arquivos:**
- Criar:
  - `MaterialPolicy`;
  - `ApproveMaterial` e `RejectMaterial`;
  - `RejectMaterialRequest`;
  - `ModerationMaterialResource`;
  - `ListModerationMaterialsController`, `ApproveMaterialController` e `RejectMaterialController`;
  - o teste `tests/Feature/Materials/ModerationTest.php`.
- Modificar: `routes/api.php`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Consome:** Tarefas 1 e 2.
- **Produz:**
  - **Rotas** (dentro de `auth:sanctum` e `can:access-moderation`):
    - `GET /api/v1/moderation/materials`;
    - `POST /api/v1/moderation/materials/{material}/approve`;
    - `POST /api/v1/moderation/materials/{material}/reject`.
  - **`MaterialPolicy::moderate(User $user, Material $material): bool`:** verdadeiro quando `$user->role->canModerate()` e (`$user->role === Role::Administrador` ou `$material->user_id !== $user->id`).
  - **`ApproveMaterial::handle(Material $material, User $moderator): bool`** e **`RejectMaterial::handle(Material $material, User $moderator, string $reason): bool`:**
    - fazem um `update` condicional com `where('status', 'PENDENTE')`;
    - devolvem `false` quando nenhuma linha muda, e o controller responde `abort(409, 'Este conteúdo já foi analisado.')`;
    - a aprovação preenche `published_at`, `decided_by` e `decided_at`.
  - **JSON de `ModerationMaterialResource`:**
    - `{id, code, title, description, url, anonymous: bool, author_name: ?string, is_own: bool, submitted_at: string, discipline: {id, name, slug}}`;
    - `author_name` é `null` quando `anonymous` e quem pede é MODERADOR;
    - o objeto `author` nunca é serializado.
  - **A fila:**
    - só traz `PENDENTE` e com soft delete nulo;
    - ordena por `created_at asc`;
    - para o MODERADOR, exclui `user_id = me`;
    - não tem paginação, com `// shortcut: fila inteira numa resposta; paginar se passar de ~200 pendentes`.

- [ ] **Passo 1: Testes que falham** (`ModerationTest`):
  - **Acesso:** o ALUNO recebe 403 na fila e nas duas ações, e sem sessão a resposta é 401.
  - **Fila:**
    - a ordem é do mais antigo para o mais novo, e só vêm os pendentes;
    - o MODERADOR não vê o próprio envio, e o ADMINISTRADOR vê o próprio com `is_own: true`.
  - **Anônimo:**
    - para o MODERADOR, um material anônimo vem com `author_name: null`, e a resposta não contém o nome de exibição do autor (`assertDontSee`);
    - o ADMINISTRADOR recebe o nome e `anonymous: true`.
  - **Aprovar:**
    - o status vira `APROVADO` e `published_at`, `decided_by` e `decided_at` são preenchidos;
    - o material aparece em `GET /disciplines/{id}/materials`.
  - **Rejeitar:**
    - sem `reason`, ou com `reason` só de espaços, a resposta é 422 com "Informe o motivo da rejeição.";
    - com 501 caracteres, a resposta é 422 com "Use no máximo 500 caracteres.";
    - com motivo válido, o status vira `REJEITADO` e o motivo é gravado.
  - **Concorrência:** aprovar e depois rejeitar o mesmo item dá 409 com "Este conteúdo já foi analisado.", e o status continua `APROVADO`, sem motivo.
  - **Próprio envio:** o MODERADOR recebe 403 ao aprovar o próprio material pela URL direta, e o ADMINISTRADOR consegue aprovar o próprio (200).

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -u sail app vendor/bin/pest tests/Feature/Materials/ModerationTest.php`. Resultado esperado: FAIL com 404.

- [ ] **Passo 3: Implementar.** Os controllers chamam `$this->authorize`, ou `Gate::authorize('moderate', $material)`, antes da Action.

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: adiciona fila de Moderação com aprovar e rejeitar material`.

---

### Tarefa 4: Termos de participação 0.2

**Arquivos:**
- Criar: `backend/resources/terms/0.2.json`, uma cópia da 0.1 com `version: "0.2"` e o parágrafo da spec §5.4 acrescentado ao fim da seção "O que outros estudantes podem ver".
- Modificar:
  - `backend/config/conectacc.php`, com o padrão `'0.2'`;
  - `backend/.env.example` (`TERMS_VERSION=0.2`) e `render.yaml` (`value: '0.2'`);
  - `backend/tests/Feature/TermsTest.php`.

- [ ] **Passo 1: Teste que falha.** Em `TermsTest`, esperar `version` `'0.2'` e que o texto contenha "Se você escolher compartilhar anonimamente".

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -u sail app vendor/bin/pest tests/Feature/TermsTest.php`. Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar** os arquivos acima. O `backend/.env` local pode ter `TERMS_VERSION=0.1`: atualizar à mão e avisar o usuário.

- [ ] **Passo 4: Rodar e ver passar.** Rodar `make test`. Resultado esperado: PASS, incluindo os testes de Cadastro, que leem a versão da config.

- [ ] **Passo 5: Commit** — `feat: Termos 0.2 com a regra de autoria e anonimato`.

---

### Tarefa 5: Materiais na página da disciplina

**Arquivos:**
- Criar: `lib/format.ts` e `features/materials/{hooks.ts,MaterialsSection.tsx,MaterialCard.tsx,ShareMaterialDialog.tsx}`.
- Modificar:
  - `features/studies/DisciplinePage.tsx`: troca a seção vazia da etapa 1 por `<MaterialsSection discipline={discipline} />` e passa a usar `hostOf`;
  - `features/profile/DiscardChangesDialog.tsx`: ganha a prop opcional `description?: string` (padrão = o texto atual do Perfil) e a prop `continueLabel?: string` (padrão "Continuar editando").

**Interfaces:**
- **Consome:** o schema regenerado (`components['schemas']['MaterialResource']`) e `Discipline` de `@/features/profile/hooks`.
- **Produz:**
  - **`lib/format.ts`:**
    - `timeAgo(iso: string, now = new Date()): string`: usa `Intl.RelativeTimeFormat('pt-BR', { numeric: 'auto' })`, com a maior unidade cheia entre ano, mês, semana, dia, hora e minuto, e abaixo de 1 minuto usa `rtf.format(0, 'second')`, que dá "agora";
    - `formatDateTime(iso: string): string`: devolve "dd/mm/aaaa às hh:mm" no fuso local;
    - `hostOf(url: string): string`: devolve o hostname sem `www.`.
  - **Hooks:**
    - `useDisciplineMaterials(disciplineId: number)`: `useInfiniteQuery` com a key `['materials', disciplineId]`, a página seguinte em `meta.next_cursor` e `initialPageParam: null`;
    - `useShareMaterial(disciplineId: number)`: mutação com `ensureCsrfCookie()` antes do POST.
  - **`MaterialCard({ material }: { material: Material })`.**
  - **`ShareMaterialDialog({ discipline, open, onOpenChange })`:**
    - React Hook Form;
    - 422 aplicado com `applyFieldErrors(setError, error.fieldErrors, ['title', 'description', 'url', 'anonymous'])`;
    - fechar com algum campo preenchido abre `DiscardChangesDialog` com a description do material;
    - o botão de envio fica desabilitado enquanto `isPending`.

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/materials.js`, com o aluno, em 1440 e em 360 px):
  - `/estudos/estrutura-de-dados-iii` mostra os 3 materiais do seeder;
  - o anônimo mostra "Compartilhado pela comunidade" e os outros mostram "Compartilhado por <nome>";
  - "Acessar material" tem `target=_blank` e o sr-only com o domínio;
  - "Compartilhar material" abre o diálogo com a disciplina já preenchida;
  - enviar vazio mostra "Informe um título para o material." e "Informe o link do material.";
  - preencher com o checkbox anônimo e dar **duplo clique** em "Enviar para análise" mostra "Material enviado para análise", e o banco tem 1 material novo (conferir via `tinker`);
  - enviar o mesmo link de novo mostra "Esse link já foi compartilhado nesta disciplina.";
  - digitar e apertar Esc abre "Descartar alterações?", e "Continuar preenchendo" mantém os dados;
  - com 21 aprovados (criar via tinker), aparece "Mostrar mais", e depois de clicar são 21 cards e o botão some;
  - um material com título de 200 caracteres sem espaço não causa rolagem horizontal em 360 px.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `node materials.js`. Resultado esperado: FAIL (a seção ainda mostra "Em breve…").

- [ ] **Passo 3: Implementar.**
  - Segue o protótipo `Disciplina` e o esboço 2.
  - Os cards ficam em lista vertical.
  - O título do card leva `[overflow-wrap:anywhere]`.

- [ ] **Passo 4: Verificar.** Rodar `make lint` e `node materials.js`. Resultado esperado: tudo PASS. Conferir também por screenshot.

- [ ] **Passo 5: Commit** — `feat: lista e envio de materiais na página da disciplina`.

---

### Tarefa 6: Página de Moderação

**Arquivos:**
- Criar: `features/moderation/{hooks.ts,ModerationPage.tsx,ReviewSheet.tsx,DecisionDialog.tsx}`.
- Modificar: `app/router.tsx`, trocando o placeholder de `/moderacao` por `<ModerationPage />`.

**Interfaces:**
- **Consome:** `ModerationMaterialResource` do schema, mais `timeAgo`, `formatDateTime` e `hostOf` (Tarefa 5).
- **Produz:**
  - **Hooks:**
    - `useModerationQueue()`: key `['moderation', 'materials']`;
    - `useApproveMaterial()` e `useRejectMaterial()`: mutações com `{ id, reason? }`. No sucesso, invalidam `['moderation', 'materials']` e `['materials', discipline.id]`.
  - **`ReviewSheet({ material, onClose })`:** usa `Sheet` do shadcn, com `side="right"` no desktop e largura total no mobile.
  - **`DecisionDialog({ kind: 'approve' | 'reject', material, onDone, onConflict })`.**
  - **Tratamento de 409:** um `ApiError` com `status === 409` abre o estado "Este conteúdo já foi analisado". O botão "Atualizar fila" faz `refetch` e fecha o painel.
  - **Aviso de resultado:** usa `FormAlert variant="success"` acima da lista, para aprovado e para rejeitado (as variantes são só `danger`, `success` e `warning`).

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/moderation.js`):
  - **Como `moderador@`:**
    - `/moderacao` lista os pendentes do mais antigo para o mais novo e não mostra o envio do próprio moderador;
    - um material anônimo aparece como "Enviado por Comunidade", e o texto do aluno não aparece na página nem no painel;
    - "Analisar" abre o painel com "Visível apenas para a moderação." e "Se aprovado, aparecerá em Estudos › Algoritmos I";
    - "Rejeitar" e depois "Rejeitar conteúdo" sem motivo mostra "Informe o motivo da rejeição.";
    - com motivo, aparece "Conteúdo rejeitado." e o item some;
    - "Aprovar" e "Aprovar conteúdo" mostra "Conteúdo aprovado com sucesso." e o material aparece na página da disciplina.
  - **Como `admin@`:**
    - vê o próprio envio com "Seu envio" e consegue aprovar;
    - um anônimo mostra o nome e "Enviado anonimamente".
  - **Como `aluno@`:** `/moderacao` redireciona para `/home`.
  - **Concorrência:** abrir o mesmo item em duas abas (`admin@` e `moderador2@`), aprovar numa e depois aprovar na outra mostra "Este conteúdo já foi analisado".
  - **Fila vazia:** mostra "Tudo certo por aqui".
  - **Mobile (360 px):** o painel não causa rolagem horizontal, mesmo com um link de 2.048 caracteres (o link leva `break-all`).

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `node moderation.js`. Resultado esperado: FAIL (a página ainda é "Em construção").

- [ ] **Passo 3: Implementar.** Segue o protótipo `Moderacao`, com as mudanças da spec §6.2: sem filtro por tipo, motivo obrigatório e sem a frase "deve ser analisado por outro administrador".

- [ ] **Passo 4: Verificar.** Rodar `make lint` e `node moderation.js`. Resultado esperado: tudo PASS. Conferir também por screenshot, no desktop e no mobile.

- [ ] **Passo 5: Commit** — `feat: adiciona página de Moderação de materiais`.

---

### Tarefa 7: Ponta a ponta e documentação

**Arquivos:** os docs listados na spec §8, na parte que vale para a 2a.

- [ ] **Passo 1: Regressão.**
  - Rodar de novo `e2e.js`, `fixes.js`, `accordion.js` e `badge.js` da etapa 1, adaptando o que a seção de materiais mudou.
  - "Ainda não há materiais compartilhados" agora vem com o botão, então o texto "Em breve…" sai das asserções.
  - Resultado esperado: tudo PASS.

- [ ] **Passo 2: Fluxo completo** (`<scratchpad>/pw/flow-2a.js`):
  1. o aluno envia um material identificado e outro anônimo;
  2. o moderador rejeita o primeiro e aprova o segundo;
  3. o aluno vê na disciplina só o aprovado, como "Compartilhado pela comunidade";
  4. o admin aprova um envio novo do aluno, e o card mostra o nome do aluno.

- [ ] **Passo 3: Docs.**
  - **`decisoes.md`:** a seção `# 55. Estudos parte 2 — 02/11/2026`, com as decisões da spec §3. Ela diz que substitui §18, RF-39, §37 e RF-73, e que resolve §52.5, §52.11 e §33 (clique).
  - **`requisitos.md`:** notas de atualização em RF-39, RF-66 e RF-73.
  - **`pendencias.md` 2.7:** os limites de material.
  - **`implementacao.md`:**
    - §8 etapa 2: marcar o formulário, o envio, a fila e os materiais aprovados;
    - §9 e §14: marcar o que entrou;
    - ajustar a §22 para 2a, 2b e 2c.
  - **`status-mvp.md`:** "Compartilhamento de material" e "Moderação" como implementados (2a).
  - **`operacao.md`:** a troca dos Termos para a 0.2 e o `MaterialSeeder`.
  - **README:** incluir Materiais e Moderação.

- [ ] **Passo 4: Rodar tudo.** Comandos: `make test` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `docs: registra Materiais e Moderação (etapa 2a)`.
