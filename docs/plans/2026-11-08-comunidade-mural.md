# Comunidade (mural de materiais) — plano de implementação

> **Execução:** tarefa por tarefa. Os passos usam checkbox (`- [ ]`) para acompanhamento.

**Objetivo:** mural cronológico em `/comunidade` com os materiais aprovados, apontando para o conteúdo original.

**Arquitetura:** um endpoint de leitura sobre `materials`, sem tabela nova e com paginação por cursor, como em `ListDisciplineMaterialsController`. A SPA ganha `features/community/`. A disciplina passa a rolar até `#material-<id>`, reaproveitando o padrão de Meus conteúdos (carregar até achar, depois rolar).

**Stack:** Laravel 13, Pest, React 19, TanStack Query (`useInfiniteQuery`), openapi-fetch.

**Spec:** `docs/specs/2026-11-08-comunidade-mural-design.md`

## Restrições globais

- Branch `feat/comunidade`. Commits em Conventional Commits, em português. Cada commit só acontece depois de os testes e o lint passarem.
- O material anônimo nunca envia o autor, para nenhum papel.
- Depois de mudar a API, rodar `make api-types`. Não rodar formatadores no front.
- Os textos de tela são os da seção 5 da spec.

## Foco de revisão

1. **Material anônimo da própria pessoa:** aparece como "Comunidade", sem `is_mine`. Testado na Tarefa 1.
2. **Material na terceira página da disciplina:** o link do mural carrega as páginas até achar o material. Testado na Tarefa 3.
3. **Material excluído entre o mural e o clique:** a disciplina abre sem erro e sem rolar. Testado na Tarefa 3.
4. **Título e nome longos no celular a 320 px:** quebram linha, sem rolagem lateral. Testado na Tarefa 3.
5. **Falha em "Mostrar mais":** os itens já carregados ficam, e "Tentar novamente" busca a próxima página. Testado na Tarefa 3.

## Interfaces

- `GET /api/v1/community/feed?cursor=` → `{ data: CommunityFeedItem[], meta: { next_cursor } }`.
- `CommunityFeedItemResource`: `id`, `code`, `title`, `url`, `published_at`, `discipline { name, slug }`, `author_name`, `author_code` e `is_mine`.
- Front: `useCommunityFeed()` em `features/community/hooks.ts`.

---

### Tarefa 1: Endpoint do mural

- [ ] Testes (`tests/Feature/Community/FeedTest.php`), cobrindo a seção 6 da spec.
- [ ] Ver falhar. Implementar o controller, o resource e a rota. Rodar `make api-types`.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 2: Página Comunidade e sidebar

- [ ] `features/community/{hooks.ts,CommunityPage.tsx,FeedItem.tsx}`, a rota `/comunidade` e o item na sidebar entre Estudos e Perfil.
- [ ] `make lint` verde. Commit.

### Tarefa 3: Ir até o material e verificação no navegador

- [ ] `MaterialsSection` lê `#material-<id>`, carrega as páginas até achar o material, rola até ele e o destaca por cerca de 2 segundos.
- [ ] Playwright: os itens do Foco de revisão, os links, os estados e as larguras de 1440, 390 e 320 px.
- [ ] Documentos da seção 7 da spec. `make test` e `make lint` verdes. Commit.
