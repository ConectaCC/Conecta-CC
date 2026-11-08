# AGENTS.md — frontend (SPA React)

Leia antes o [`AGENTS.md`](../AGENTS.md) da raiz. Decisões de stack: `docs/stack-e-decisoes-tecnicas.md` (decisões 3, 11 e 12). Identidade visual: `docs/identidade-visual.md`.

## Stack

React 19 + TypeScript (Vite), React Router (`createBrowserRouter`), TanStack Query, React Hook Form, cliente HTTP tipado gerado do OpenAPI (`openapi-typescript` + `openapi-fetch`), shadcn/ui (Radix) + Tailwind v4 e ícones `lucide-react`. As fontes são auto-hospedadas: Plus Jakarta Sans nos títulos (`font-display`) e Inter no corpo. Não há modo escuro.

## Estrutura de `src/`

| Pasta | Papel |
|---|---|
| `app/router.tsx` | todas as rotas, com os guards `RequireAuth`, `GuestOnly` e `RequireModeration` |
| `features/<area>/` | uma pasta por área, com nome em inglês: `auth`, `home`, `profile`, `studies`, `materials`, `moderation`, `my-contents`, `notifications`, `students`, `settings`. Guarda as páginas, os componentes da área e um `hooks.ts` com as consultas |
| `components/` | componentes compartilhados da casa: `TextField`, `FormAlert`, `Skeleton`, `Chip`, `StatusTag`, `TagInput`, `DisciplinePicker`, `Brand` e `PageHeader` (cabeçalho padrão das páginas internas, `decisoes.md` 73) |
| `components/ui/` | componentes gerados pelo CLI do shadcn (`docker compose exec frontend npx --yes shadcn@latest add <nome> --yes`). Não reescrever à mão |
| `components/layout/` | `AppLayout`, `Sidebar`, `Header`, `UserAvatar` e a guarda de alterações não salvas (`LeaveGuardProvider` e `useUnsavedChangesGuard`) |
| `lib/useUrlSearch.ts`, `lib/useScrollToHashItem.ts` | busca com atraso e filtros no endereço; rolagem até `#<prefixo>-<id>` com destaque, usada por materiais e oportunidades |
| `lib/api/` | `client.ts` (openapi-fetch com cookie e `X-XSRF-TOKEN`), `errors.ts` (`call`, `ApiError`, `NetworkError`) e `schema.d.ts` (**gerado**) |

- As rotas ficam em português (`/estudos`, `/estudos/:slug`, `/perfil?editar=disciplinas`).
- Use primeiro um componente da casa. Só depois um do shadcn. Nunca crie um componente que duplica um existente.

## Dados do servidor

- **Consultas:** cada feature tem hooks em `hooks.ts`, no padrão `useQuery({ queryKey: ['profile'], queryFn: () => call(api.GET('/profile')) })`.
- **Tipos:** vêm de `components['schemas']['XResource']` em `@/lib/api/schema`. Nunca declare à mão o formato de algo que a API devolve.
- **Reaproveite hooks entre features**, em vez de duplicar consulta. Exemplo: Estudos usa `useProfile()` e `useDisciplines()` de `features/profile/hooks.ts`, juntos em `useStudies()`.
- **Catálogos que não mudam** (disciplinas) usam `staleTime: Infinity`. Um filtro sobre uma lista pequena e já carregada roda no navegador, sem endpoint novo.
- **Padrões globais do `queryClient`:** `retry: false` e `refetchOnWindowFocus: false`.
- **Mutações:** chamam `await ensureCsrfCookie()` antes do envio. No sucesso, atualizam o cache (`setQueryData`) e invalidam `['me']` quando header ou Home dependem do dado.
- **Conflitos (409):** `ApiError.code` traz o motivo (`material_cancelled`, `link_taken`…). Decida pelo código, nunca pelo texto da mensagem.
- **Mudou a API?** Rode `make api-types`. Nunca edite `schema.d.ts` nem `openapi.json` à mão.

## Estados de tela

| Situação | Padrão |
|---|---|
| Carregando | `Skeleton` com o formato do conteúdo, dentro de um contêiner com `aria-busy="true"`. O cabeçalho e o breadcrumb continuam visíveis |
| Erro ao carregar | `FormAlert variant="warning"` com o botão "Tentar novamente", que refaz só as consultas com erro |
| Erro com dados já em cache | mostra o aviso **acima** do conteúdo e **nunca esconde** o que já carregou |
| 422 | `applyFieldErrors(setError, error.fieldErrors, campos)` aplica os erros no formulário (React Hook Form) |
| 401 | o middleware do cliente limpa `['me']` e o guard leva ao Login |
| Falha de rede (`NetworkError`) | alerta com "Tentar novamente", sem perder o que foi digitado |
| Alterações não salvas | guarda de saída (`useUnsavedChangesGuard`) e diálogo "Descartar alterações?" (`DiscardChangesDialog`) |
| Enviando | botão desabilitado, com spinner e texto no gerúndio ("Salvando...", "Entrando...") |

Os textos de tela são **exatamente** os da spec e do protótipo.

## Visual

- **Cores:** use os tokens de `index.css` (`navy-900`, `navy-950`, `action`, `functional`, `glow`, `ice`, `danger`, `success`…) e as cores do shadcn (`foreground`, `muted-foreground`). Não use hex solto, salvo onde o padrão já existe.
- **Status de conteúdo:** sempre com `StatusTag`, que guarda a cor de cada status (`docs/decisoes.md` 58). `Chip` é só para rótulos neutros. Não defina a cor de um status em outra tela.
- **Card padrão:** `rounded-2xl border border-blue-200/70 bg-white p-6|p-7`.
- **Títulos:** `font-display font-bold`. O `h1` da página é `text-4xl` (ou `text-3xl sm:text-4xl` quando o título vem de dado e pode ser longo).
- **Grades responsivas:** `grid gap-4 sm:grid-cols-2 lg:grid-cols-3`, com 1 coluna no mobile.
- **Larguras:** o layout precisa funcionar em **390, 360 e 320 px sem rolagem horizontal**. Palavras longas em títulos levam `[overflow-wrap:anywhere]` e `min-w-0` dentro de flex.

## Acessibilidade (obrigatória)

- O ESLint usa `jsx-a11y`.
- Todo campo tem label visível (`TextField`), `aria-invalid` e `aria-describedby` para erro e ajuda.
- A hierarquia de títulos é real (`h1` > `h2` > `h3`) e cada seção tem `aria-labelledby`.
- Contadores e resultados que mudam usam `aria-live="polite"`. Alertas usam `role="alert"`, e sucesso usa `role="status"`.
- **Link externo:**
  - abre com `target="_blank" rel="noopener noreferrer"`;
  - leva o ícone `ExternalLink` com `aria-hidden`;
  - leva um `span.sr-only` com "(abre em nova guia, <host>)".
- **Foco depois de uma ação:** quando o botão clicado some, leve o foco para o aviso de resultado ou para um título (`tabIndex={-1}`), usando `onCloseAutoFocus` nos diálogos.
- **Diálogos e menus:** use `showCloseButton={false}` (o X do shadcn diz "Close") e um botão em português. `DropdownMenu` que abre diálogo usa `modal={false}`.
- Um card clicável é **um único link**. O breadcrumb fica em `nav aria-label="Trilha"`, com `aria-current="page"` no item atual.
- Foco visível, alvos de toque de pelo menos 44px no mobile, e estados que não dependem só de cor.

## Estilo de código

- **O projeto não usa Prettier. Não rode formatadores** (`npx prettier` reformata tudo com outro estilo). O estilo é aspas simples, ponto e vírgula, imports com `@/` e linhas de até cerca de 130 colunas.
- Componentes em `PascalCase.tsx`, com export nomeado (`export function StudiesPage()`). Hooks em `useX.ts` ou no `hooks.ts` da feature.
- Funções utilitárias pequenas ficam na própria feature. Exemplo: `normalizeSearch` em `features/studies/normalize.ts`.
- Lógica de comparação de texto usa `includes`, nunca regex montada a partir do que a pessoa digitou.

## Verificação

- **Sempre:** `make lint` (ESLint + `tsc`). O CI também roda o build.
- **Análise estática:** TypeScript com `strict` e `noUncheckedIndexedAccess`, e ESLint com `strictTypeChecked`. Promessa que não é esperada leva `void` (`void navigate('/home')`, `void client.invalidateQueries(...)`).
- **Testes (Vitest e Testing Library):** `make test-front`.
  - Ficam ao lado do código, em `*.test.ts(x)`. O fuso dos testes é o do câmpus.
  - `src/test/render.tsx` monta a tela com Router e React Query (`renderWithProviders`), com data router para telas com aviso de alterações não salvas (`renderWithDataRouter`) e para hooks (`renderHookWithClient`, que também diz quais chaves foram invalidadas).
  - `src/test/api.ts` troca o cliente da API por respostas prontas (`mockApi('POST', reply(422, {...}))`).
  - Teste o que a pessoa vê: rótulos, mensagens exatas, links e o que **não** aparece (autor de material anônimo e de oportunidade).
- **Antes do PR, `make mutate-front`:** StrykerJS só nas linhas de `frontend/src` alteradas em relação à `origin/main`, com nota mínima de 80%. Textos fixos (classes CSS e textos de tela) não são mutados. `src/components/ui`, o `schema.d.ts` e o `main.tsx` ficam fora. O CI roda o mesmo.
- **Ponta a ponta (Playwright):** `make e2e`, com o ambiente de pé (`make up`). Só local, fora do CI.
  - As specs ficam em `frontend/e2e/` e rodam num container com o navegador, na rede do host, para a sessão do Sanctum valer em `localhost:5173`. Isso funciona no Docker do Linux; no Docker Desktop, a rede do host precisa estar ligada.
  - Os testes criam materiais e oportunidades com "e2e" no título, no banco de dev. O `make e2e` limpa o cache antes, para não esbarrar no limite de envios.
  - Cubra em desktop (1440×900) e mobile (390×844), e inclua as entradas do "Foco de revisão" do plano.
- Confira o visual por screenshot antes de dar a entrega como pronta.

## Painel `/admin`

O painel é renderizado pelo Laravel (Filament), fora da SPA. A sidebar só tem um link comum para ele, visível ao ADMINISTRADOR, e o proxy do `vite.config.ts` encaminha as rotas do painel em desenvolvimento.
