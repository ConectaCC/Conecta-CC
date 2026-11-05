# AGENTS.md — ConectaCC (geral)

Guia para agentes e pessoas que vão mexer no código. Ele resume o que **já foi decidido**. Cada pasta tem o seu guia com os detalhes:

- [`backend/AGENTS.md`](backend/AGENTS.md) — API Laravel
- [`frontend/AGENTS.md`](frontend/AGENTS.md) — SPA React
- [`docs/AGENTS.md`](docs/AGENTS.md) — documentação, specs e planos

Quando este guia e os documentos de `docs/` discordarem, valem os documentos: `decisoes.md`, `requisitos.md`, `fluxos.md` e `stack-e-decisoes-tecnicas.md`. Nesse caso, corrija este guia.

## O projeto

Plataforma acadêmica para estudantes de Ciência da Computação do IFSul – Câmpus Passo Fundo. É um monorrepo:

| Pasta | O que é |
|---|---|
| `backend/` | API Laravel em `/api/v1`. Também serve a SPA compilada e o painel `/admin` |
| `frontend/` | SPA React + TypeScript (Vite) |
| `docs/` | produto, decisões, specs e planos de cada entrega |
| `design/` | referência do protótipo |
| `docker/` | imagem Sail de desenvolvimento e script do Postgres de teste |
| `Dockerfile`, `render.yaml` | imagem de produção (multiestágio) e deploy no Render |
| `compose.yaml`, `Makefile` | ambiente de desenvolvimento |

**Arquitetura API-first:** toda regra de negócio e de autorização fica no backend e no banco. O front nunca é fonte de verdade. A SPA e a API ficam na mesma origem, com sessão por cookie (Sanctum).

## Fonte de verdade do produto

- **Escopo atual:** Oportunidade, Comunidade e busca de estudantes estão fora do MVP (`decisoes.md` 56). O que falta está em `docs/status-mvp.md`.
- **Requisitos:** `docs/requisitos.md` (RF-xx). **Fluxos:** `docs/fluxos.md`. **Decisões:** `docs/decisoes.md`.
- **Visual:** o protótipo congelado (link em `design/README.md`). Layout, textos e estados de tela seguem o protótipo, salvo decisão registrada em contrário.
- **Funcionalidade prototipada não é funcionalidade implementada.** Confira `docs/status-mvp.md` e `docs/implementacao.md`.
- **Link institucional só depois de confirmado.** Sem URL confirmada, o texto é "Link oficial a confirmar", nunca um link inventado.
- **Referência, não cópia:** o ConectaCC guarda links e metadados de fontes oficiais (ementas, sistemas do IFSul), nunca uma cópia do conteúdo (`decisoes.md` 53.1).

## Idioma

- Tudo o que a pessoa usuária vê está em **português do Brasil**: textos de tela, mensagens de erro da API e validações.
- Os nomes no código (classes, funções, variáveis, pastas de feature) ficam em **inglês**: `UpdateProfile`, `StudiesPage`, `features/studies/`.
- Rotas do front, comentários, commits, specs e planos ficam em **português**: `/estudos`, `/perfil?editar=disciplinas`.

## Como uma entrega é feita

1. **Design:** perguntas ao time até fechar o escopo, depois a spec em `docs/specs/AAAA-MM-DD-<tema>-design.md`. Nada é implementado antes de a spec ser aprovada.
2. **Plano:** `docs/plans/AAAA-MM-DD-<tema>.md`, com tarefas pequenas, arquivos, interfaces e verificação.
3. **Branch:** `feat/<tema>` a partir da `main`, com commits pequenos por tarefa.
4. **Docs no fim:** atualizar `decisoes.md`, `pendencias.md`, `implementacao.md`, `status-mvp.md` e o README conforme a spec (ver `docs/AGENTS.md`).
5. **PR para a `main`:** o CI precisa estar verde (checks `backend`, `frontend`, `docker`, `gitleaks`). O merge é feito com **merge commit**, como nos PRs anteriores.

Uma escolha que o time não fez e o agente fez fica marcada na spec como **(escolha do Claude)**, para ser revista.

Uma funcionalidade que depende de outra ainda inexistente é dividida em etapas, e cada etapa entra na `main` funcionando de ponta a ponta. Exemplo: o envio de material só entra junto com a Moderação de material (`decisoes.md` 54.1).

## Git

- Os commits seguem **Conventional Commits** em português (`feat:`, `fix:`, `docs:`, `chore:`, `ci:`, `test:`). Commits feitos por agente terminam com a linha `Co-Authored-By` do agente.
- Nunca fazer push, abrir PR ou fazer merge sem pedido explícito. A `main` é protegida.
- Nunca commitar `.env`, dumps de banco nem segredos. O repositório é público e tem varredura de segredos (gitleaks).

## Ambiente de desenvolvimento

Só é preciso ter Docker. O resto roda nos containers.

| Comando | O que faz |
|---|---|
| `make setup` | primeira vez: dependências, `.env`, chave, banco com seeders |
| `make up` / `make down` | sobe e derruba o ambiente |
| `make test` | testes da API (Pest, contra PostgreSQL real) |
| `make lint` | Pint, Larastan, ESLint e `tsc` |
| `make api-types` | regenera o OpenAPI e os tipos do front (obrigatório depois de mudar a API) |
| `make migrate` | roda migrations pendentes |
| `make fresh` | **apaga** o banco de dev e recria com seeders. Não rodar sem pedir |
| `make shell` | terminal no container do Laravel |

- Endereços locais: app em http://localhost:5173, documentação da API em http://localhost:8000/docs/api, Mailpit em http://localhost:8026.
- **Porta 5433 ocupada** por outro projeto: suba com `FORWARD_DB_PORT=5434 docker compose up -d`. Isso não afeta os containers.
- Contas de teste (senha `password`): `aluno@`, `moderador@`, `moderador2@` e `admin@academico.ifsul.edu.br`.
- Para testar um fluxo, prefira ajustar só o usuário de teste (por exemplo, pelo `tinker`) em vez de recriar o banco inteiro.

## Princípios de implementação

- **A menor mudança que resolve tudo.** Reaproveite o que já existe (hooks, componentes, Actions, helpers) antes de criar algo novo. Não adicione dependência por poucas linhas.
- **Nada "para depois":** sem opções, abstrações ou campos que ninguém pediu. Exemplo: não existe flag "área de materiais" porque todas as disciplinas têm materiais (`decisoes.md` 54.2).
- **Atalho com limite conhecido** leva o comentário `// shortcut: <o limite>, <quando trocar>`. Exemplo: os tetos provisórios de texto em `UpdateProfileRequest`.
- **Comentário só para o porquê** que o código não mostra, em uma linha.
- **Nunca cortar:** validação nas fronteiras de confiança, autorização no backend, tratamento de erro que evita perda de dados, acessibilidade.

## Produção

- **Hospedagem:** Render (plano gratuito, imagem Docker) e Neon (Postgres 17).
- **Migrations:** rodam na inicialização do container (`migrate --force`).
- **Sem Shell no Render:** comandos administrativos rodam por workflow do GitHub Actions (por exemplo, "Promover usuário").
- **Sentry:** identifica o usuário só pelo ID interno. Sem e-mail, nome ou corpo de requisição.
- **Detalhes de operação:** `docs/operacao.md`.
