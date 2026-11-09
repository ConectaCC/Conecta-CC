# Estudos parte 2, etapa 2c: Notificações — plano de implementação

> **Execução:** tarefa por tarefa. Os passos usam checkbox (`- [ ]`) para acompanhamento.

**Objetivo:** o autor recebe uma notificação quando o material ou a alteração dele é aprovado ou rejeitado. O sino do Header mostra o contador e abre o painel. Clicar numa notificação marca como lida e leva ao conteúdo.

**Arquitetura:**

- **Backend:** usa as notificações nativas do Laravel (canal `database`, tabela `notifications`, `Notifiable`, que já está no `User`). São quatro classes de notificação, criadas na mesma transação da decisão, e três endpoints.
- **Front:** um componente `NotificationsBell` no Header, com painel (`Popover` do shadcn), polling de 60 segundos e nova consulta a cada troca de rota.

**Stack:** a de sempre. O `Popover` é gerado pelo CLI do shadcn (`npx shadcn add popover`), sobre o `radix-ui` já instalado. Sem dependência nova.

**Spec:** `docs/specs/2026-11-02-estudos-parte-2-design.md`, nas partes 2c: §3.10, §4.3, §5.1 (linhas 2c), §5.3 (notificação na transação), §6.1 (texto de sucesso), §6.2 ("O autor será notificado."), §6.4 e §7. Leia a spec e siga `AGENTS.md`, `backend/AGENTS.md` e `frontend/AGENTS.md`.

**Base:** a branch `feat/notificacoes` saiu da `feat/meus-conteudos` (etapa 2b), porque usa o `?item=` de Meus conteúdos. Quando o PR da 2b entrar na `main`, rebasear sobre a `main` antes de abrir o PR da 2c.

## Restrições globais

- **Git:**
  - branch `feat/notificacoes`;
  - Conventional Commits em português;
  - sem push.
- **Fora desta etapa:**
  - página própria de notificações (§33);
  - notificações de Publicação e Oportunidade, que entram com os módulos delas;
  - e-mail e tempo real (WebSocket).
- **Eventos (exatamente quatro):** material aprovado, material rejeitado, alteração aprovada e alteração rejeitada. Só o autor é notificado.
- **`data` gravado:**
  - `{kind: 'material_approved'|'material_rejected'|'revision_approved'|'revision_rejected', material_id, code, discipline_name, discipline_slug}`;
  - numa alteração, `material_id` e `code` são **os do material original**, para o link de Meus conteúdos.
- **Textos, exatos (spec §6.4):**

| `kind` | Título | Detalhe |
|---|---|---|
| `material_approved` | "Seu material foi aprovado" | "O material enviado para <disciplina> foi aprovado." |
| `material_rejected` | "Seu material não foi aprovado" | "O material enviado para <disciplina> foi analisado." |
| `revision_approved` | "Sua alteração foi aprovada" | "A alteração do material em <disciplina> foi aprovada." |
| `revision_rejected` | "Sua alteração não foi aprovada" | "A alteração enviada para <disciplina> foi analisada." |

- **Destino do clique:**
  - os eventos `*_approved` levam a `/estudos/<slug>`;
  - os eventos `*_rejected` levam a `/perfil/meus-conteudos?item=<code>`.
- **Painel:**
  - título "Notificações" e o botão "Marcar todas como lidas";
  - cada item mostra um ícone (✓ ou ✕), título, detalhe, tempo relativo (`timeAgo`) e o selo "Nova" quando não lida;
  - vazio: "Você ainda não tem notificações." e "Quando algo que você enviar for analisado, o resultado aparece aqui.";
  - carregando: "Carregando notificações…";
  - erro: "Não foi possível carregar as notificações agora." com "Tentar novamente".
- **Sino:**
  - `aria-label` "Notificações" ou "Notificações, N não lidas" ("1 não lida" no singular);
  - badge com o número, ou "9+" acima de 9;
  - `aria-expanded` vem do Popover.
- **Textos que mudam nesta etapa:**
  - sucesso do envio: "Você receberá uma notificação quando o material for analisado.", mantendo "Você pode acompanhar a análise em Meus conteúdos." como segunda frase;
  - avisos da Moderação: "Conteúdo aprovado com sucesso." e "Conteúdo rejeitado." passam a terminar com "O autor será notificado.".
- **Lista:** traz as 20 mais recentes e `unread_count` (de todas as não lidas, não só das 20).
- **Front:**
  - sem runner de testes, então a prova é `make lint` mais um roteiro Playwright fora do repositório;
  - não rodar Prettier;
  - não rodar `make fresh`.

## Foco de revisão

1. **Aprovar e rejeitar ao mesmo tempo, ou repetir a decisão (409)** → só a decisão que valeu gera notificação. Nunca há duas notificações para o mesmo envio (Tarefa 1).
2. **Material anônimo** → a notificação vai para o autor, e nenhum texto dela traz o nome dele ou de quem decidiu (Tarefa 1).
3. **Usuário tenta marcar a notificação de outro como lida** (`POST /notifications/{id}/read` com um id alheio) → 404, e a notificação continua não lida (Tarefa 2).
4. **Mais de 20 notificações e 12 não lidas** → o painel mostra 20 e o badge mostra "9+". Depois de "Marcar todas como lidas", o badge some (Tarefas 2 e 3).
5. **Clicar numa notificação de rejeição cujo item está na 2ª página de Meus conteúdos** → abre o item expandido, porque o `?item=` da 2b já busca as páginas seguintes (Tarefa 3).

---

## Mapa de arquivos

```
backend/
  database/migrations/2026_11_02_000003_create_notifications_table.php   (php artisan make:notifications-table)
  app/Notifications/MaterialDecided.php                                   uma classe, com o kind no construtor
  app/Actions/{ApproveMaterial,RejectMaterial}.php                        notifica dentro da transação
  app/Http/Resources/NotificationResource.php                             título, detalhe e link a partir do kind
  app/Http/Controllers/Api/V1/{ListNotifications,ReadNotification,ReadAllNotifications}Controller.php
  routes/api.php
  database/seeders/NotificationSeeder.php                                 2 notificações do aluno (dev)
  tests/Feature/Notifications/NotificationsTest.php
frontend/src/
  components/ui/popover.tsx                                               gerado pelo CLI do shadcn
  features/notifications/{hooks.ts,NotificationsBell.tsx}
  components/layout/Header.tsx                                            usa NotificationsBell
  features/materials/ShareMaterialDialog.tsx, features/moderation/ModerationPage.tsx   textos
docs/  decisoes.md, implementacao.md (§16), status-mvp.md, README.md
```

**Escolha da implementação:** em vez de quatro classes de notificação, uma só (`MaterialDecided`), com o `kind` no construtor. Os quatro eventos diferem só no `kind`, então quatro classes seriam código repetido. A spec §4.3 citava quatro classes. Registrar no ledger.

---

### Tarefa 1: Notificar o autor na decisão

**Arquivos:**
- Criar: a migration (`docker compose exec -u sail app php artisan make:notifications-table`), `MaterialDecided.php` e os testes de geração em `tests/Feature/Notifications/NotificationsTest.php`.
- Modificar: `ApproveMaterial` e `RejectMaterial` (`RejectMaterial` passa a usar `DB::transaction`).

**Interfaces:**
- **Produz:**
  - `MaterialDecided(string $kind, Material $original)`: `via` devolve `['database']`, e `toArray` devolve o `data` das Restrições globais.
  - O `$original` é o próprio material, ou o `revisionOf` quando for uma alteração.
  - O autor é notificado com `$material->author->notify(new MaterialDecided(...))` **dentro** da transação e **só quando** o update condicional mudou uma linha.

- [ ] **Passo 1: Testes que falham:**
  - cada um dos quatro eventos gera exatamente uma notificação para o autor, com o `kind`, `code`, `discipline_slug` e `material_id` certos (na alteração, os do original);
  - a segunda decisão (409) não gera outra notificação;
  - um 409 de `link_taken` não gera notificação;
  - num material anônimo, a notificação vai para o autor, e o `data` não contém o nome dele nem o de quem decidiu;
  - nenhum outro usuário recebe a notificação.

- [ ] **Passo 2: Rodar e ver falhar.** Comando: `docker compose exec -T -u sail app vendor/bin/pest tests/Feature/Notifications`. Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar.**

- [ ] **Passo 4: Rodar e ver passar.** Rodar o comando do Passo 2, `make test` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: notifica o autor quando o material ou a alteração é analisado`.

---

### Tarefa 2: API de notificações

**Arquivos:**
- Criar: `NotificationResource`, os três controllers, `NotificationSeeder` (chamado pelo `DatabaseSeeder`, que pode ser rodado sozinho e é idempotente) e os testes de API no mesmo arquivo de teste.
- Modificar: `routes/api.php`.
- Regenerar: `make api-types`.

**Interfaces:**
- **Produz:**
  - **Rotas** (dentro de `auth:sanctum`):
    - `GET /notifications`: `{data: NotificationResource[], unread_count: int}`, com as 20 mais recentes (`latest()->limit(20)`) e `unread_count` igual a `$user->unreadNotifications()->count()`;
    - `POST /notifications/{id}/read`: procura em `$user->notifications()->whereKey($id)`, e não achar dá 404. Marca como lida e devolve 204;
    - `POST /notifications/read-all`: `$user->unreadNotifications->markAsRead()` e devolve 204.
  - **JSON de `NotificationResource`:** `{id: string, title: string, detail: string, ok: bool, read: bool, link: string, created_at: string}`. O título, o detalhe e o link saem do `kind` (tabela das Restrições globais).

- [ ] **Passo 1: Testes que falham:**
  - a lista traz só as do usuário, das mais recentes para as mais antigas, no máximo 20, com `unread_count` contando todas as não lidas (crie 25, com 12 não lidas);
  - os textos e o `link` de cada `kind` são os da tabela;
  - marcar uma como lida a tira do `unread_count`;
  - marcar a de outro usuário dá 404 e ela continua não lida;
  - "ler todas" zera o contador só do usuário;
  - sem sessão, as três rotas dão 401.

- [ ] **Passo 2: Rodar e ver falhar.** Resultado esperado: FAIL com 404.

- [ ] **Passo 3: Implementar.**

- [ ] **Passo 4: Rodar e ver passar.** Rodar `make test`, `make api-types` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 5: Commit** — `feat: adiciona a API de notificações`.

---

### Tarefa 3: Sino e painel no Header

**Arquivos:**
- Criar:
  - `components/ui/popover.tsx` (`docker compose exec frontend npx --yes shadcn@latest add popover --yes`);
  - `features/notifications/{hooks.ts,NotificationsBell.tsx}`.
- Modificar: `components/layout/Header.tsx`, trocando o ícone solto pelo `NotificationsBell` e tirando o comentário "só o ícone…".

**Interfaces:**
- **Produz:**
  - **`useNotifications()`:** `useQuery` com a key `['notifications']`, `refetchInterval: 60_000` e `refetchIntervalInBackground: false`.
  - **`useReadNotification()` e `useReadAllNotifications()`:** chamam `ensureCsrfCookie` e invalidam `['notifications']`.
  - **Troca de rota:** `NotificationsBell` chama `refetch()` num efeito sobre `useLocation().pathname`.
  - **Clique num item:** fecha o painel, chama o "ler" (sem esperar a resposta, para não atrasar a navegação) e faz `navigate(item.link)`.
  - **O painel:**
    - é o `PopoverContent` com `align="end"` e largura `w-[min(380px,calc(100vw-1rem))]`;
    - cada item é um `<button>` com o título, o detalhe, o tempo e "Nova";
    - itens não lidos têm fundo `bg-ice` e título em `font-semibold`;
    - Esc fecha (o Radix já cuida disso).

- [ ] **Passo 1: Roteiro que falha** (`<scratchpad>/pw/notifications.js`):
  - com 12 não lidas do aluno (via `php()`), o sino tem `aria-label` "Notificações, 12 não lidas" e o badge "9+";
  - o painel mostra 20 itens;
  - clicar numa notificação de rejeição leva a `/perfil/meus-conteudos?item=<code>` com o item expandido, e o contador cai para 11;
  - clicar numa de aprovação leva a `/estudos/<slug>`;
  - "Marcar todas como lidas" faz o badge sumir e o rótulo virar "Notificações";
  - sem notificações, aparece o texto de vazio;
  - Esc fecha o painel e devolve o foco ao sino;
  - **polling:** depois de uma decisão nova feita por outro usuário (via `php()`), a troca de rota atualiza o contador;
  - em 360px, o painel não causa rolagem horizontal.

- [ ] **Passo 2: Rodar e ver falhar.** Resultado esperado: FAIL.

- [ ] **Passo 3: Implementar.** Segue o protótipo `Home-Notificacoes`.

- [ ] **Passo 4: Verificar.** Rodar `make lint` e o roteiro. Resultado esperado: tudo PASS. Conferir também por screenshot, no desktop e no mobile.

- [ ] **Passo 5: Commit** — `feat: sino e painel de notificações no Header`.

---

### Tarefa 4: Textos, regressão e documentação

**Arquivos:**
- Modificar: `ShareMaterialDialog.tsx` e `ModerationPage.tsx` (os textos das Restrições globais).
- Docs:
  - `decisoes.md`: acrescentar à 55.6 a classe única de notificação e a mensagem de erro do painel;
  - `implementacao.md` §16: marcar o que entrou;
  - `status-mvp.md`: Notificações implementadas (material);
  - README.

- [ ] **Passo 1: Ajustar os roteiros antigos** que esperam os textos anteriores (`materials.js`, `moderation.js`, `fixes-2b.js`) e rodar todos: `materials`, `moderation`, `regression`, `flow-2a`, `my-contents`, `edit-delete`, `moderation-2b`, `flow-2b` e `notifications`. Resultado esperado: tudo PASS.

- [ ] **Passo 2: Fluxo completo** (`flow-2c.js`):
  1. o aluno envia um material;
  2. o moderador rejeita;
  3. o sino do aluno mostra 1, e o clique leva ao item com o motivo;
  4. o aluno envia de novo, e o admin aprova;
  5. a notificação leva à disciplina, onde o material aparece.

- [ ] **Passo 3: Rodar tudo.** Comandos: `make test` e `make lint`. Resultado esperado: tudo verde.

- [ ] **Passo 4: Commit** — `docs: registra Notificações de material (etapa 2c)`.
