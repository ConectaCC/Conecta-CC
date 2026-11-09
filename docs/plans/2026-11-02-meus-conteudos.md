# Estudos parte 2, etapa 2b: Meus conteúdos, Editar, Excluir e Cancelar — plano de implementação

> **Execução:** tarefa por tarefa. Os passos usam checkbox (`- [ ]`) para acompanhamento.

**Objetivo:** o autor acompanha os próprios envios em `/perfil/meus-conteudos`, cancela um envio pendente, edita um material aprovado (a alteração passa pela Moderação) e exclui um material aprovado. A Moderação passa a analisar alterações, com a versão publicada e a proposta lado a lado.

**Arquitetura:**

- **Backend:**
  - Uma alteração é uma nova linha em `materials`, com `revises_id` (spec §4.2). A fila continua sendo `status = PENDENTE`.
  - `ApproveMaterial` passa a aplicar a alteração no original, na mesma transação.
  - Endpoints novos: `GET /me/materials`, `POST /materials/{material}/cancel`, `PUT /materials/{material}` e `DELETE /materials/{material}`.
- **Front:**
  - a página Meus conteúdos;
  - o menu ⋯ no card do material;
  - o diálogo de envio reaproveitado para editar;
  - a Moderação com a etiqueta "Alteração".

**Stack:** a de sempre. O menu ⋯ usa o `DropdownMenu` do pacote `radix-ui`, que já está instalado. Sem dependência nova.

**Spec:** `docs/specs/2026-11-02-estudos-parte-2-design.md`. Esta etapa cobre as partes 2b de §3.9, §4.2, §5.1 (linhas 2b), §5.3, §6.1 (menu ⋯ e texto de sucesso), §6.2 (alteração e cancelado), §6.3 e §7. Leia a spec e siga `AGENTS.md`, `backend/AGENTS.md` e `frontend/AGENTS.md`.

## Restrições globais

- **Git:**
  - branch `feat/meus-conteudos` (já criada a partir da `main`, com a 2a);
  - Conventional Commits em português;
  - sem push.
- **Fora desta etapa:**
  - Notificações (2c);
  - cancelar uma alteração pendente (o autor espera a análise);
  - retirar material aprovado (ADMIN) e solicitar revisão (MODERADOR).
- **Regras:**
  - Meus conteúdos tem paginação com "Mostrar mais", de 20 em 20 (decisão do Marcelo, 02/11/2026), no mesmo padrão da lista de materiais da disciplina;
  - só o autor cancela, edita ou exclui (403 para os outros);
  - cancelar só funciona em `PENDENTE` de um original (409 nos outros casos);
  - editar e excluir só funcionam em `APROVADO`, sem soft delete e que não seja alteração (409 nos outros casos);
  - a edição usa o mesmo `throttle:materials`;
  - `anonymous` é ignorado na edição;
  - um material só pode ter uma alteração `PENDENTE` por vez.
- **Mensagens da API, exatas:**
  - "Já existe uma alteração deste material em análise." (409);
  - "Este conteúdo foi cancelado pelo autor." (409 na Moderação, quando o item foi cancelado);
  - "O link desta alteração já foi compartilhado nesta disciplina." (409 ao aprovar uma alteração cujo link passou a repetir);
  - "Este envio não pode ser cancelado.", "Este material não pode ser editado." e "Este material não pode ser excluído." (409);
  - o resto é igual à 2a ("Esse link já foi compartilhado nesta disciplina." etc.).
- **Textos do front, exatos:**
  - **Meus conteúdos:**
    - "Perfil / Meus conteúdos", "Meus conteúdos" e "Acompanhe o que você enviou e a análise da moderação.";
    - filtros: Todos, Em andamento, Aprovados, Rejeitados e Excluídos;
    - status: "Em análise", "Aprovado", "Rejeitado", "Cancelado" e "Excluído";
    - "Anônimo" e "Alteração em análise desde dd/mm/aaaa";
    - "MAT-00042 · Material · Estudos › <disciplina>" e "Enviado em dd/mm/aaaa";
    - ações: "Ver dados enviados", "Cancelar envio", "Ver conteúdo", "Editar" e "Excluir";
    - "Motivo da rejeição:";
    - "Alteração não aprovada em dd/mm/aaaa: <motivo>" (último resultado de alteração rejeitada, até a próxima alteração);
    - vazio: "Você ainda não enviou conteúdos." e "Nenhum conteúdo neste filtro.";
    - carregando: "Carregando seus conteúdos…";
    - erro: "Não foi possível carregar seus conteúdos agora." e "Tentar novamente".
  - **Diálogos:**
    - **Cancelar:** "Cancelar envio?", "O material sai da fila de análise e não será publicado." e os botões "Voltar" e "Cancelar envio" ("Cancelando…").
    - **Excluir:** "Excluir material?", "O material deixará de aparecer na disciplina. Você ainda poderá consultá-lo em Meus conteúdos." e os botões "Cancelar" e "Excluir material" ("Excluindo…").
    - **Editar:** título "Editar material", sem o checkbox de anônimo.
      - Sucesso: "Alteração enviada para análise", "A versão atual continua visível até a alteração ser aprovada." e "Voltar".
    - **Falha:** "Não foi possível concluir a ação. Tente novamente.".
  - **Sucesso do envio (muda nesta etapa):** "Você pode acompanhar a análise em Meus conteúdos.", com "Meus conteúdos" como link para `/perfil/meus-conteudos`.
  - **Moderação:**
    - a etiqueta "Alteração", e no painel "Versão publicada" e "Alteração proposta";
    - conflito de cancelamento: "Este conteúdo foi cancelado pelo autor", com "Atualize a fila para continuar." e "Atualizar fila".
  - **Perfil:** o link "Meus conteúdos" no cabeçalho, ao lado de "Editar perfil".
  - **Menu ⋯:** o botão tem `aria-label` "Ações do material <título>", e as opções são "Editar" e "Excluir".
- **Front:**
  - sem runner de testes, então a prova é `make lint` mais um roteiro Playwright fora do repositório (`<scratchpad>/pw/`, que reaproveita `login.js` com `php()` e `clearLimits()`);
  - não rodar Prettier;
  - não rodar `make fresh`.

## Foco de revisão

1. **Outro usuário tenta cancelar, editar ou excluir o material de alguém** pela URL direta → 403, e nada muda (Tarefa 2).
2. **O autor cancela enquanto um moderador está com o painel aberto** → a decisão do moderador recebe 409 com "Este conteúdo foi cancelado pelo autor." e a tela mostra isso, em vez de "já foi analisado" (Tarefas 3 e 6).
3. **Uma edição aprovada cujo link passou a repetir outro envio da disciplina** → 409 com a mensagem, a alteração continua pendente e o original fica intacto, sem erro 500 (Tarefa 3).
4. **Editar um material anônimo** → a versão publicada continua anônima depois da aprovação, mesmo se o payload mandar `anonymous: false` (Tarefas 2 e 3).
5. **Excluir um material com alteração pendente** → o material some da disciplina, a alteração sai da fila e o item aparece como "Excluído" em Meus conteúdos (Tarefas 2 e 7).

---

## Mapa de arquivos

```
backend/
  app/Policies/MaterialPolicy.php                       + manage()
  app/Actions/{CancelMaterial,ReviseMaterial,DeleteMaterial}.php
  app/Actions/{ApproveMaterial,RejectMaterial}.php      alteração e cancelado
  app/Http/Requests/StoreMaterialRequest.php            link repetido ignora o próprio material na edição
  app/Http/Resources/{MyMaterialResource,ModerationMaterialResource}.php
  app/Http/Controllers/Api/V1/{ListMyMaterials,CancelMaterial,UpdateMaterial,DeleteMaterial}Controller.php
  app/Http/Controllers/Api/V1/{ApproveMaterial,RejectMaterial}Controller.php   409 com a mensagem certa
  routes/api.php
  database/seeders/MaterialSeeder.php                   + rejeitado, cancelado e alteração pendente do aluno
  tests/Feature/Materials/{MyMaterials,ManageMaterial,ModerateRevision}Test.php
frontend/src/
  components/ui/dropdown-menu.tsx                       no padrão shadcn, sobre radix-ui
  features/my-contents/{hooks.ts,MyContentsPage.tsx,ContentItem.tsx,ConfirmActionDialog.tsx}
  features/materials/{MaterialCard.tsx,ShareMaterialDialog.tsx,hooks.ts}   menu ⋯, modo edição, texto de sucesso
  features/moderation/{ReviewSheet.tsx,ModerationPage.tsx,DecisionDialog.tsx}  alteração e cancelado
  features/profile/ProfilePage.tsx                      link Meus conteúdos
  app/router.tsx                                        /perfil/meus-conteudos
docs/  decisoes.md, implementacao.md, status-mvp.md, README.md
```

---

### Tarefa 1: Meus conteúdos na API

**Arquivos:**
- Criar: `MyMaterialResource`, `ListMyMaterialsController` e `tests/Feature/Materials/MyMaterialsTest.php`.
- Modificar: `routes/api.php` e `MaterialSeeder`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Produz:**
  - **Rota:** `GET /api/v1/me/materials?status=&cursor=`, com `status` em `all` (padrão), `pending`, `approved`, `rejected` ou `deleted`. Os dois parâmetros são documentados com `#[QueryParameter]`. Um valor inválido vira `all`.
  - **Paginação:** `cursorPaginate(20)`. O JSON tem o formato `{data, links, meta}`, igual ao da lista da disciplina, e o front lê `meta.next_cursor`.
  - **Itens:**
    - só os originais do usuário logado (`revises_id` nulo), incluindo os com soft delete (`withTrashed`);
    - ordenados por `created_at desc, id desc`.
  - **Filtros:**
    - `pending`: status `PENDENTE`, sem soft delete;
    - `approved`: `APROVADO`, sem soft delete;
    - `rejected`: `REJEITADO`;
    - `deleted`: com soft delete;
    - `all`: tudo.
  - **JSON de `MyMaterialResource`:**
    - `{id, code, title, description, url, anonymous, status, rejection_reason, submitted_at, discipline: {id, name, slug}, pending_revision, rejected_revision}`;
    - `status` é `'PENDENTE'|'APROVADO'|'REJEITADO'|'CANCELADO'|'EXCLUIDO'` e vale `EXCLUIDO` quando há soft delete;
    - `pending_revision` é `{title, description, url, submitted_at}|null`;
    - `rejected_revision` é `{reason, decided_at}|null`: a alteração rejeitada mais recente, só quando não há alteração pendente mais nova.
  - **Seeder** (idempotente, por `url`). Acrescenta para o `aluno@`:
    - um material rejeitado ("Link pede login no Moodle.");
    - um cancelado;
    - um aprovado com alteração pendente.

- [ ] **Passo 1: Testes que falham** (`MyMaterialsTest`):
  - lista só os próprios, mais recentes primeiro, com `code` e `discipline.slug`;
  - não lista as alterações como itens;
  - os cinco filtros trazem exatamente os itens esperados (criar um de cada situação);
  - o soft delete aparece com `status: 'EXCLUIDO'` em `all` e em `deleted`;
  - `pending_revision` vem preenchido quando há alteração pendente;
  - `rejected_revision` traz o motivo da última alteração rejeitada e volta a `null` quando há uma pendente mais nova;
  - `rejection_reason` vem no item rejeitado;
  - **paginação:** com 21 envios, a primeira página traz 20 e `meta.next_cursor`, e a segunda traz 1 com `next_cursor` nulo. O filtro vale em todas as páginas;
  - sem sessão, a resposta é 401.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -T -u sail app vendor/bin/pest tests/Feature/Materials/MyMaterialsTest.php`. Resultado esperado: FAIL com 404.

- [ ] **Passo 3: Implementar.** Carregar as alterações com `with` para evitar N+1. Um `hasMany` `revisions()` em `Material` (por `revises_id`) é o suficiente.

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: adiciona a lista de Meus conteúdos na API`.

---

### Tarefa 2: Cancelar, editar e excluir na API

**Arquivos:**
- Criar:
  - as Actions `CancelMaterial`, `ReviseMaterial` e `DeleteMaterial`;
  - os controllers `CancelMaterialController`, `UpdateMaterialController` e `DeleteMaterialController`;
  - o teste `tests/Feature/Materials/ManageMaterialTest.php`.
- Modificar: `MaterialPolicy` (com `manage`), `StoreMaterialRequest` e `routes/api.php`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Consome:** `StoreMaterialRequest::materialData()` e o `DUPLICATE_URL` da 2a.
- **Produz:**
  - **`MaterialPolicy::manage(User $user, Material $material): bool`:** `$material->user_id === $user->id && $material->revises_id === null`.
  - **Rotas** (dentro de `auth:sanctum`):
    - `POST /materials/{material}/cancel`, que devolve 200 com `MyMaterialResource`;
    - `PUT /materials/{material}`, com `throttle:materials`, que devolve 201 com `MyMaterialResource` do original (com `pending_revision`);
    - `DELETE /materials/{material}`, que devolve 204.
    - Os três usam `Gate::authorize('manage', $material)`.
    - O binding precisa achar material com soft delete só onde fizer sentido: no `DELETE` de um material já excluído, a resposta é 404.
  - **`StoreMaterialRequest`:** a checagem de link repetido ignora o material da rota `{material}`, quando existir (`where('id', '!=', ...)`). Na edição, `discipline` vem de `$material->discipline`.
  - **`CancelMaterial::handle(Material $material): bool`:** faz um update condicional `PENDENTE → CANCELADO`. Devolve `false` quando nenhuma linha muda, e o controller responde 409 "Este envio não pode ser cancelado.".
  - **`ReviseMaterial::handle(Material $original, array $data): Material`:**
    - recusa com 409 "Este material não pode ser editado." quando o original não está `APROVADO` ou tem soft delete;
    - recusa com 409 "Já existe uma alteração deste material em análise." quando já há alteração `PENDENTE`;
    - cria a linha com `revises_id`, o mesmo `discipline_id`, `user_id` e `anonymous` do original e status `PENDENTE`.
  - **`DeleteMaterial::handle(Material $material): void`:**
    - recusa com 409 "Este material não pode ser excluído." quando o material não está `APROVADO`;
    - numa transação, cancela as alterações `PENDENTE` (que viram `CANCELADO`) e faz o soft delete.

- [ ] **Passo 1: Testes que falham** (`ManageMaterialTest`):
  - **Cancelar:**
    - o autor cancela o pendente, e o status vira `CANCELADO`;
    - cancelar um aprovado ou rejeitado dá 409;
    - outro usuário (aluno, moderador ou admin) recebe 403 e nada muda;
    - o item cancelado some da fila de Moderação.
  - **Editar:**
    - o autor edita o aprovado e cria uma alteração `PENDENTE`;
    - a versão pública continua a antiga (`GET /disciplines/{id}/materials` mostra o título antigo);
    - o mesmo link do próprio material é aceito;
    - um link de outro material ativo da disciplina recebe 422 com "Esse link já foi compartilhado nesta disciplina.";
    - uma segunda edição com alteração pendente recebe 409 com a mensagem;
    - editar um pendente, rejeitado ou excluído dá 409;
    - `anonymous: false` num material anônimo cria a alteração com `anonymous: true`;
    - outro usuário recebe 403;
    - o 11º envio ou edição na hora recebe 429;
    - os casos de validação da 2a também valem (título vazio dá 422).
  - **Excluir:**
    - o autor exclui o aprovado: soft delete, 204, o material some da lista pública e aparece com `EXCLUIDO` em Meus conteúdos;
    - a alteração pendente vira `CANCELADO` e sai da fila;
    - excluir um pendente dá 409;
    - outro usuário recebe 403;
    - excluir de novo dá 404.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -T -u sail app vendor/bin/pest tests/Feature/Materials/ManageMaterialTest.php`. Resultado esperado: FAIL com 404 ou 405.

- [ ] **Passo 3: Implementar.** Os erros 409 de Action usam `abort(409, '<mensagem>')` no controller ou uma `HttpException` lançada pela Action, no mesmo padrão da 2a.

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: permite cancelar, editar e excluir o próprio material`.

---

### Tarefa 3: Moderação de alterações e de envio cancelado

**Arquivos:**
- Modificar:
  - `ApproveMaterial`, `RejectMaterial`, `ApproveMaterialController` e `RejectMaterialController`;
  - `ModerationMaterialResource`, que ganha `revision_of`.
- Criar: `tests/Feature/Materials/ModerateRevisionTest.php`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Produz:**
  - **`ModerationMaterialResource`:** ganha `revision_of: {title: string, description: string|null, url: string}|null`, com a versão publicada do original. A fila passa a fazer `with('revisionOf')`.
  - **`ApproveMaterial::handle`:** quando `revises_id` não é nulo, numa `DB::transaction`:
    1. faz o update condicional da alteração para `APROVADO` (com `decided_*` e `published_at`);
    2. copia `title`, `description` e `url` para o original.
    - Uma `UniqueConstraintViolationException` faz rollback e o controller responde 409 "O link desta alteração já foi compartilhado nesta disciplina.".
  - **Conflito no controller:** quando `handle` devolve `false`, recarrega o status. Se for `CANCELADO`, responde 409 "Este conteúdo foi cancelado pelo autor.". Se não, responde 409 "Este conteúdo já foi analisado.".
  - **`RejectMaterial`:** sem mudança de regra. A alteração rejeitada só muda de status, e o controller usa a mesma distinção de 409.

- [ ] **Passo 1: Testes que falham** (`ModerateRevisionTest`):
  - a fila traz a alteração com `revision_of` igual à versão publicada;
  - aprovar a alteração copia os campos para o original e mantém o `published_at` dele;
  - a lista pública mostra o título novo e continua com um só item;
  - rejeitar a alteração mantém o original e grava o motivo na alteração;
  - aprovar uma alteração cujo link foi tomado depois por outro envio dá 409 com a mensagem, a alteração continua `PENDENTE` e o original não muda;
  - a alteração de um material anônimo continua anônima depois de aprovada (`author_name` nulo na lista pública);
  - aprovar ou rejeitar um envio cancelado dá 409 com "Este conteúdo foi cancelado pelo autor.";
  - o MODERADOR não vê na fila a alteração do próprio material.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -T -u sail app vendor/bin/pest tests/Feature/Materials/ModerateRevisionTest.php`. Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar.**

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: Moderação analisa alterações e reconhece envio cancelado`.

---

### Tarefa 4: Página Meus conteúdos

**Arquivos:**
- Criar: `features/my-contents/{hooks.ts,MyContentsPage.tsx,ContentItem.tsx,ConfirmActionDialog.tsx}`.
- Modificar:
  - `app/router.tsx`, com `{ path: '/perfil/meus-conteudos', element: <MyContentsPage /> }`;
  - `features/profile/ProfilePage.tsx`, com o link "Meus conteúdos" (`Button variant="outline" asChild`) ao lado de "Editar perfil".

**Interfaces:**
- **Consome:** `MyMaterialResource` do schema e `formatDateTime` de `lib/format`. Acrescentar a `lib/format` a função `formatDate(iso): string`, que devolve "dd/mm/aaaa".
- **Produz:**
  - **Hooks:**
    - `useMyMaterials(filter: MyFilter)`: `useInfiniteQuery` com a key `['my-materials', filter]`, a página seguinte em `meta.next_cursor` e `initialPageParam: null`;
    - `useCancelMaterial()` e `useDeleteMaterial()`, que no sucesso invalidam `['my-materials']`, `['materials', discipline.id]` e `['moderation', 'materials']`.
  - **`type MyFilter = 'all' | 'pending' | 'approved' | 'rejected' | 'deleted'`.** O filtro fica na URL (`?filtro=`) e não muda o `?item=`.
  - **`ConfirmActionDialog({ open, title, description, confirmLabel, busyLabel, cancelLabel, busy, error, onConfirm, onCancel })`:** um diálogo de confirmação reaproveitado por Cancelar e Excluir.
  - **`ContentItem({ item, expanded, onToggle, onCancel, onEdit, onDelete })`:**
    - "Ver dados enviados" é um botão com `aria-expanded` que mostra título, descrição, link (sr-only "(abre em nova guia, …)") e "Compartilhado anonimamente" quando for o caso;
    - "Ver conteúdo" é um `Link` para `/estudos/<slug>`.
  - **"Mostrar mais":** aparece abaixo da lista enquanto houver `next_cursor`. Uma falha ao carregar a próxima página mostra o aviso "Não foi possível carregar seus conteúdos agora." com "Tentar novamente", sem esconder o que já carregou.
  - **`?item=MAT-00042`:** abre o item expandido e faz `scrollIntoView` nele. Se o item não estiver nas páginas carregadas, a página chama `fetchNextPage` até achar o item ou acabar a lista. Se não achar, a lista fica normal, sem erro.
  - **Status:** as etiquetas usam os tokens existentes: `warning` para Em análise, `success` para Aprovado, `danger` para Rejeitado e neutro para Cancelado e Excluído.
  - **Filtros:** um grupo de botões com `aria-pressed`, dentro de `role="group" aria-label="Filtrar por situação"`.

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/my-contents.js`, com o aluno e os itens do seeder):
  - o Perfil mostra o link "Meus conteúdos", que leva a `/perfil/meus-conteudos`;
  - a página lista os itens do seeder, com o código no formato `MAT-\d{5}`, "Em análise", "Aprovado", "Rejeitado" e "Cancelado";
  - o rejeitado mostra "Motivo da rejeição: Link pede login no Moodle.";
  - o aprovado com alteração pendente mostra "Alteração em análise desde";
  - o filtro "Rejeitados" mostra só os rejeitados, e "Cancelado" não aparece em "Em andamento";
  - "Ver dados enviados" expande e mostra o link;
  - `?item=<código do rejeitado>` abre aquele item já expandido;
  - **paginação:** com 25 envios do aluno (criados via `php()` com links `pw-`), aparecem 20 itens e "Mostrar mais"; depois do clique são 25 e o botão some;
  - `?item=` de um item que só está na 2ª página também abre o item expandido;
  - "Cancelar envio" e depois "Cancelar envio?" e "Cancelar envio" fazem o item virar "Cancelado";
  - "Excluir", depois "Excluir material?" e "Excluir material", fazem o item virar "Excluído" e o material sumir da disciplina;
  - não há rolagem horizontal em 360px.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `node my-contents.js`. Resultado esperado: FAIL (a rota não existe).

- [ ] **Passo 3: Implementar.** Segue o esboço 3 aprovado.

- [ ] **Passo 4: Verificar.** Rodar `make lint` e `node my-contents.js`. Resultado esperado: tudo PASS. Conferir também por screenshot, no desktop e no mobile.

- [ ] **Passo 5: Commit** — `feat: adiciona a página Meus conteúdos`.

---

### Tarefa 5: Editar e excluir pelo card, com o menu ⋯

**Arquivos:**
- Criar: `components/ui/dropdown-menu.tsx`, no padrão shadcn (`radix-nova`), sobre o `DropdownMenu` do `radix-ui`. Exportar só o que for usado: `DropdownMenu`, `DropdownMenuTrigger`, `DropdownMenuContent` e `DropdownMenuItem`.
- Modificar:
  - `features/materials/MaterialCard.tsx`, com o menu quando `is_mine`;
  - `features/materials/ShareMaterialDialog.tsx`, com o modo edição;
  - `features/materials/hooks.ts`, com `useReviseMaterial()`;
  - `features/my-contents/ContentItem.tsx`, onde "Editar" abre o mesmo diálogo.

**Interfaces:**
- **Produz:**
  - **`ShareMaterialDialog`:** ganha a prop opcional `editing?: { id: number; title: string; description: string | null; url: string }`. Com ela:
    - o título vira "Editar material";
    - os campos vêm preenchidos e o checkbox de anônimo some;
    - o envio vira `PUT /materials/{id}`;
    - a tela de sucesso mostra "Alteração enviada para análise" e "A versão atual continua visível até a alteração ser aprovada.", com o botão "Voltar".
    - Um 409 vira o problema geral com a mensagem do servidor.
    - O `discipline` continua sendo usado para o rótulo "Disciplina".
  - **Texto de sucesso do envio novo:** "Você pode acompanhar a análise em Meus conteúdos.", com o link.
  - **`useReviseMaterial()`:** no sucesso, invalida `['my-materials']`.
  - **Menu ⋯ no `MaterialCard`:** usa `DropdownMenu`, e o botão tem `aria-label` "Ações do material <título>". "Editar" abre o diálogo em modo edição, e "Excluir" abre o `ConfirmActionDialog` (Tarefa 4) e chama `useDeleteMaterial`.

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/edit-delete.js`):
  - o aluno vê o ⋯ só nos próprios cards, inclusive no anônimo, e não nos cards de outros autores;
  - o ⋯ do moderador não aparece nos materiais do aluno;
  - "Editar" abre "Editar material" já preenchido e sem o checkbox de anônimo;
  - salvar mostra "Alteração enviada para análise", e o card continua com o título antigo;
  - editar de novo mostra "Já existe uma alteração deste material em análise.";
  - "Excluir" e depois "Excluir material" fazem o card sumir;
  - um envio novo mostra o link "Meus conteúdos" no sucesso;
  - o menu abre e funciona pelo teclado (Enter no ⋯, setas, Enter em Editar).

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `node edit-delete.js`. Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar.** Segue o esboço 2 aprovado.

- [ ] **Passo 4: Verificar.** Rodar `make lint` e `node edit-delete.js`. Resultado esperado: tudo PASS. Conferir também por screenshot.

- [ ] **Passo 5: Commit** — `feat: edição e exclusão do material pelo card`.

---

### Tarefa 6: Moderação de alterações na tela

**Arquivos:**
- Modificar: `features/moderation/{ReviewSheet.tsx,ModerationPage.tsx,DecisionDialog.tsx}`.

**Interfaces:**
- **Consome:** o `revision_of` do schema (Tarefa 3).
- **Produz:**
  - **Item da fila:** ganha a etiqueta "Alteração" quando `revision_of` existe.
  - **Painel:**
    - numa alteração, mostra as duas versões lado a lado (empilhadas no mobile): "Versão publicada", com a versão atual, e "Alteração proposta", com o item;
    - cada versão mostra título, descrição e o link com o domínio;
    - o restante do painel não muda.
  - **Conflito:**
    - o `onConflict(message: string)` do `DecisionDialog` passa a receber a mensagem do servidor;
    - o diálogo de conflito usa o título "Este conteúdo foi cancelado pelo autor" quando a mensagem é a de cancelamento, e "Este conteúdo já foi analisado" nos outros casos;
    - o texto "Atualize a fila para continuar." e o botão "Atualizar fila" continuam.

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/moderation-2b.js`):
  - o admin vê a alteração do seeder com "Alteração", e o painel mostra "Versão publicada" e "Alteração proposta" com os títulos certos;
  - aprovar faz a disciplina mostrar o título novo;
  - com o painel do moderador aberto num envio do aluno, o aluno cancela o envio (pela API via `php()` ou pela página Meus conteúdos), e o "Aprovar conteúdo" do moderador mostra "Este conteúdo foi cancelado pelo autor";
  - não há rolagem horizontal em 360px com o painel de alteração aberto.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `node moderation-2b.js`. Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar.** Segue o esboço 4 aprovado.

- [ ] **Passo 4: Verificar.** Rodar `make lint`, `node moderation-2b.js` e `node moderation.js` (regressão da 2a). Resultado esperado: tudo PASS.

- [ ] **Passo 5: Commit** — `feat: Moderação mostra alteração lado a lado e envio cancelado`.

---

### Tarefa 7: Ponta a ponta e documentação

**Arquivos:**
- Docs: `decisoes.md`, `implementacao.md`, `status-mvp.md` e `README.md`.

- [ ] **Passo 1: Regressão.** Rodar `materials.js`, `moderation.js`, `regression.js` e `flow-2a.js`, ajustando o texto de sucesso do envio, que mudou. Resultado esperado: tudo PASS.

- [ ] **Passo 2: Fluxo completo** (`<scratchpad>/pw/flow-2b.js`):
  1. o aluno envia um material;
  2. o moderador aprova;
  3. o aluno edita pelo ⋯;
  4. o moderador rejeita a alteração;
  5. em Meus conteúdos, o item aparece com "Alteração não aprovada" e o motivo;
  6. o aluno edita de novo, e o admin aprova;
  7. a disciplina mostra o título novo;
  8. o aluno exclui o material, que vira "Excluído" em Meus conteúdos.

- [ ] **Passo 3: Docs.**
  - **`decisoes.md`:** acrescentar à seção 55 as escolhas desta etapa:
    - sem cancelar alteração pendente;
    - "Alteração não aprovada" no item;
    - Meus conteúdos com "Mostrar mais" de 20 em 20;
    - mensagens de 409.
  - **`implementacao.md`:** marcar Meus conteúdos e as ações do autor na §8, e ajustar a §22.
  - **`status-mvp.md`:** linhas de Meus conteúdos e ações do autor, se existirem. Se não existirem, acrescentar.
  - **README:** incluir Meus conteúdos.

- [ ] **Passo 4: Rodar tudo.** Comandos: `make test` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `docs: registra Meus conteúdos e ações do autor (etapa 2b)`.
