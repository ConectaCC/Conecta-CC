# Operação — ConectaCC

Como subir o ambiente, rodar os testes e operar a produção.

## Desenvolvimento

Pré-requisito: Docker com Compose. PHP, Composer e Node não precisam estar instalados na máquina.

Primeira vez:

    make setup

Depois:

    make up        # sobe tudo
    make down      # derruba

Acesse **http://localhost:5173**. O Vite faz proxy de `/api`, `/sanctum` e `/up` para o Laravel, então o navegador vê uma única origem, como em produção.

| Endereço | O que é |
|---|---|
| http://localhost:5173 | aplicação |
| http://localhost:8000/docs/api | documentação da API (OpenAPI) |
| http://localhost:8026 | Mailpit (e-mails capturados) |
| localhost:5433 | PostgreSQL (usuário `sail`, senha `password`, banco `conectacc`) |

As portas do Postgres e do Mailpit na máquina podem ser trocadas pelas variáveis `FORWARD_DB_PORT` e `FORWARD_MAILPIT_DASHBOARD_PORT` (por exemplo, num arquivo `.env` na raiz do repositório).

### Contas de teste

Criadas pelos seeders (só em desenvolvimento). Senha de todas: `password`.

| E-mail | Papel |
|---|---|
| aluno@academico.ifsul.edu.br | ALUNO |
| moderador@academico.ifsul.edu.br | MODERADOR |
| moderador2@academico.ifsul.edu.br | MODERADOR |
| admin@academico.ifsul.edu.br | ADMINISTRADOR |

O `MaterialSeeder` cria materiais de exemplo (aprovados, pendentes, rejeitado, cancelado e uma alteração pendente), e o `NotificationSeeder` cria duas notificações para o `aluno@`. Os dois podem ser rodados sozinhos, sem recriar o banco: `docker compose exec -u sail app php artisan db:seed --class=MaterialSeeder`.

### Comandos

| Comando | O que faz |
|---|---|
| `make migrate` | roda migrations pendentes |
| `make fresh` | recria o banco com os seeders |
| `make test` | testes da API (Pest, contra PostgreSQL) |
| `make lint` | Pint, Larastan (nível 10), ESLint e TypeScript |
| `make mutate` | antes do PR: cobertura de linhas de 100% e teste de mutação nas classes do back alteradas em relação à `origin/main`, com nota mínima de 90%. Outra base: `make mutate BASE=main` |
| `make test-front` | testes do front (Vitest e Testing Library) |
| `make mutate-front` | antes do PR: StrykerJS nas linhas do front alteradas em relação à `origin/main`, com nota mínima de 80%. O relatório fica em `frontend/reports/mutation/index.html` |
| `make e2e` | testes de ponta a ponta (Playwright) com o ambiente de pé. Roda num container na rede do host (Docker do Linux, ou Docker Desktop com a rede do host ligada). Cria materiais e oportunidades com "e2e" no título no banco de dev e limpa o cache antes, por causa do limite de envios |
| `make api-types` | exporta o OpenAPI e regenera os tipos do front; rode sempre que mudar a API e commite `frontend/openapi.json` e `frontend/src/lib/api/schema.d.ts` |
| `make shell` | terminal dentro do container do Laravel |

Para ter os tipos no editor, rode também `npm install` dentro de `frontend/` na sua máquina.

Os limites de tentativas (5 por minuto no Login e no Cadastro) usam o cache do Laravel. Para zerá-los durante testes manuais: `docker compose exec -u sail app php artisan cache:clear`.

## Variáveis de ambiente

| Variável | Onde | Para quê |
|---|---|---|
| `APP_KEY` | Render | chave do Laravel (`php artisan key:generate --show`) |
| `APP_URL` | Render | URL pública do serviço |
| `DB_URL` | Render | connection string do Neon (com `sslmode=require`) |
| `SANCTUM_STATEFUL_DOMAINS` | Render | host público, sem `https://` |
| `ALLOWED_EMAIL_DOMAINS` | Render (já no `render.yaml`) | domínios aceitos no Cadastro, separados por vírgula |
| `TERMS_VERSION` | Render (já no `render.yaml`) | versão vigente dos Termos (arquivo em `backend/resources/terms/`) |
| `SENTRY_LARAVEL_DSN` | Render | DSN do Sentry para o backend (vazio = desligado) |
| `VITE_SENTRY_DSN` | Render | DSN do Sentry para o front, usado no build (vazio = desligado) |
| `RENDER_DEPLOY_HOOK_URL` | GitHub (secret) | URL do deploy hook do Render |
| `DATABASE_URL` | GitHub (secret) | connection string do Neon, usada só pelo workflow "Promover usuário" |

## Primeira publicação

Todas as contas pertencem à conta Google do projeto (decisão 9).

1. **Neon:** criar um projeto com Postgres 17 na região **AWS US East 1 (N. Virginia)**, só com o serviço Postgres (sem Better Auth/Neon Auth: o login é do Laravel). Em Connect, desligar **Connection pooling** e copiar a connection string direta, com `sslmode=require`. Não rodar o roteiro `neon init`/`neon deploy` que o Neon sugere: ele é para apps JavaScript e baixaria a senha de produção para um `.env` local.
2. **Render:** New → Blueprint → apontar para o repositório. O `render.yaml` cria o serviço em **Virginia**, ao lado do Neon; preencher as variáveis marcadas como `sync: false`. O `APP_KEY` sai de `docker compose exec -u sail app php artisan key:generate --show`, rodado no seu terminal e guardado direto no cofre.
3. **Render:** em Settings → Deploy Hook, copiar a URL e cadastrá-la no GitHub como secret `RENDER_DEPLOY_HOOK_URL`.
4. **GitHub:** cadastrar o secret `DATABASE_URL` com a connection string do Neon.
5. **Sentry (opcional):** criar dois projetos (Laravel e React) e preencher os DSNs no Render.
6. Fazer merge na `main`. O CI roda e, se passar, o workflow Deploy chama o Render. As migrations rodam na inicialização do container.
7. **Primeiro ADMINISTRADOR:** a pessoa se cadastra pelo site; depois, no GitHub, Actions → "Promover usuário" → Run workflow → e-mail e papel `ADMINISTRADOR`.

## Configuração do repositório (admin)

1. Settings → Code security → **Secret scanning: Enable** e **Push protection: Enable**.
2. Settings → Code security → **Dependabot alerts** e **Dependabot security updates: Enable**. O `.github/dependabot.yml` já abre PRs semanais de versão.
3. Settings → Branches → regra para `main`: exigir pull request e os checks `backend`, `frontend`, `docker` e `gitleaks`.

## Segurança

- **Antes de cada publicação:** `docker compose exec -u sail app composer audit` e `docker compose exec frontend npm audit --omit=dev` sem avisos.
- **Cabeçalhos e CSP:** a resposta da SPA leva a política de conteúdo (`backend/routes/web.php`). Depois do deploy, confira no navegador (DevTools → Console) que não há aviso "Content Security Policy".
- **Cookies:** em produção, `SESSION_SECURE_COOKIE=true` (já no `render.yaml`). O cookie da sessão é `HttpOnly` e `SameSite=Lax`.
- **`APP_DEBUG=false` em produção:** um erro mostra a página genérica, nunca o rastro da pilha.
- **Segredos:** só nas variáveis do Render e nos secrets do GitHub. Nenhum `.env` entra na imagem (`.dockerignore`).

## Auditoria

O `/admin` → **Auditoria** mostra cada ação que mudou dados, com quem fez e quando, no horário de Brasília. Os eventos não podem ser alterados nem apagados: a trigger `audit_events_immutable` recusa `UPDATE`, `DELETE` e `TRUNCATE`. Remover um evento, por exemplo por um pedido ligado à LGPD, exige uma migration deliberada. O `make fresh` continua funcionando no ambiente local, porque apaga a tabela inteira.

## Mudar o papel de alguém em produção

**Pelo painel (caminho normal):** um ADMINISTRADOR entra em `/admin`, abre **Usuários** e usa **Alterar papel**. Ninguém muda o próprio papel. Cada mudança gera a linha de log `Papel alterado`, só com IDs internos e os papéis.

**Pelo workflow (emergência ou primeiro ADMINISTRADOR):** Actions → **Promover usuário** → Run workflow, informando e-mail e papel. O e-mail é mascarado no log e o comando não o imprime. Só quem tem permissão de escrita no repositório consegue disparar. O Render gratuito não tem Shell, por isso o comando roda no GitHub Actions.

Os arquivos públicos do Filament são gerados pelo `composer` (script `post-autoload-dump`), inclusive no build da imagem, e não entram no Git. Depois de atualizar a branch no ambiente local, rode `composer install` dentro do container se o painel abrir sem estilo.

## Trocar os Termos de participação

1. Criar `backend/resources/terms/<nova versão>.json` com o texto.
2. Atualizar `TERMS_VERSION` no `render.yaml`, no Render, em `backend/.env.example`, no padrão de `config/conectacc.php` e em `backend/phpunit.xml`. No ambiente local, atualizar também o `backend/.env`.
3. Quem estiver no meio de um cadastro verá "Os Termos de participação foram atualizados. Leia e aceite novamente.".

A versão vigente é a **0.2** (02/11/2026), que acrescentou a regra de autoria e anonimato dos conteúdos.

## Atualizar o catálogo de disciplinas

O catálogo vem de `backend/resources/catalog/matriz-2023.json` e é gravado no banco por uma migration de dados, que roda sozinha no deploy. Para mudar uma disciplina (nome, carga horária, link da ementa) ou incluir outra:

1. Editar o JSON. O link da ementa aponta para o PDF oficial no site do IFSul: não copiar o PDF (`decisoes.md` 53.1).
2. Criar uma migration que chame `DisciplineCatalog::sync(resource_path('catalog/matriz-2023.json'))`. A sincronização atualiza pelo nome e não duplica.
3. Fazer merge na `main`: o deploy aplica a migration.

Renomear uma disciplina cria uma nova pelo nome. A antiga continua no banco até ser removida por uma migration própria.
