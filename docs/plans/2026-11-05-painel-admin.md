# Painel `/admin` — plano de implementação

> **Para agentes:** use `superpowers:subagent-driven-development` ou `superpowers:executing-plans` para executar tarefa por tarefa. Os passos usam checkbox (`- [ ]`).

**Objetivo:** criar o painel exclusivo do ADMINISTRADOR, com alteração de papéis e consulta somente leitura das moderações.

**Arquitetura:** um painel Filament 5 em `/admin`, renderizado pelo Laravel, na mesma sessão da SPA. A regra de alteração de papel fica em uma Action. Moderações só lê os materiais existentes. A SPA ganha um link, e a API não muda.

**Stack:** Laravel 13, Filament 5, Livewire 4, Pest, React 19.

**Spec:** `docs/specs/2026-11-05-painel-admin-design.md`

## Restrições globais

- Branch `feat/painel-admin`. Commits em Conventional Commits, em português, terminando com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
- Não entram: exclusão de conta, retirada, revisão ou reversão de moderação, catálogo de disciplinas, contadores, edição de dados de usuário, redefinição de senha, auditoria em tela.
- Sem migrations e sem mudança na API.
- `role` continua fora do `Fillable`.
- Não rodar formatadores no front.
- Os textos de tela são os das seções 4 a 6 da spec.
- Só o commit de cada tarefa depois de o teste e o lint passarem de verdade (código de saída), nunca encadeado com `;`.

## Foco de revisão

1. **Chamada direta à ação escondida:** um ADMINISTRADOR forjando a chamada Livewire da própria linha não muda o próprio papel. Testado na Tarefa 2.
2. **Conta sem nome de exibição em Moderações e Usuários:** a tela abre e mostra "—", sem erro 500. Testado nas Tarefas 2 e 3.
3. **Material cujo autor ou decisor existe, em todas as combinações de status e exclusão:** a coluna Situação nunca quebra, e `deleted_at` vale mais que o status. Testado na Tarefa 3.
4. **URLs do Livewire e arquivos do Filament:** não caem no fallback da SPA, em desenvolvimento nem na imagem de produção. Testado nas Tarefas 1 e 5.
5. **Validação vazia no login e no modal de papel:** a mensagem aparece em português, nunca como `validation.required`. Testado na Tarefa 1 e no navegador.

## Mapa de arquivos

| Arquivo | Papel |
|---|---|
| `backend/composer.json`, `composer.lock` | Filament |
| `backend/app/Providers/Filament/AdminPanelProvider.php` | configuração do painel |
| `backend/bootstrap/providers.php` | registra o provider |
| `backend/app/Filament/Pages/Auth/Login.php` | normaliza o e-mail no login |
| `backend/app/Models/User.php` | `FilamentUser` |
| `backend/app/Actions/ChangeUserRole.php` | regra de alteração de papel |
| `backend/app/Filament/Resources/Users/` | tela de Usuários |
| `backend/app/Filament/Resources/Materials/` | tela de Moderações |
| `backend/resources/views/errors/403.blade.php` | página 403 em português |
| `backend/lang/pt_BR/validation.php` | mensagens padrão de validação |
| `backend/routes/web.php` | fallback da SPA |
| `backend/.gitignore`, `Dockerfile` | arquivos públicos do Filament |
| `frontend/src/features/auth/roles.ts`, `frontend/src/components/layout/Sidebar.tsx` | link do painel |
| `backend/tests/Feature/Admin/` | testes do painel |

---

### Tarefa 1: Painel e acesso

**Arquivos:** provider, `Login.php`, `User.php`, `403.blade.php`, `lang/pt_BR/validation.php`, `routes/web.php`, `.gitignore`, `Dockerfile`, `backend/tests/Feature/Admin/AdminAccessTest.php`.

**Interfaces:**
- Produz: `User::canAccessPanel(Panel $panel): bool`, painel de id `admin` em `/admin`.

- [ ] Commitar antes a spec, o plano e a política de exclusão em `docs:`.
- [ ] Instalar `filament/filament:^5.0` e confirmar `make test` verde.
- [ ] Escrever `AdminAccessTest`:
  - sem sessão, `/admin` redireciona para `/admin/login`;
  - ALUNO e MODERADOR recebem 403, com "Você não tem permissão para acessar esta página.";
  - ADMINISTRADOR chega ao painel;
  - sessão criada por `POST /api/v1/login` abre o painel;
  - ao perder o papel, a requisição seguinte responde 403;
  - login do painel aceita `ADMIN@...` com espaços nas pontas e recusa ALUNO;
  - login vazio mostra mensagem em português, sem `validation.required`;
  - depois de `PUT /api/v1/me/password`, a sessão atual continua abrindo o painel;
  - `/home` continua servindo a SPA, e as rotas do painel e do Livewire não caem no fallback.
- [ ] Ver os testes falharem.
- [ ] Implementar o painel: login próprio, cores e marca do ConectaCC, sem modo escuro e sem o painel inicial padrão. Item "Voltar ao ConectaCC" no menu do usuário, para `/home`.
- [ ] Implementar `canAccessPanel`, o login normalizado, a página 403 e as validações.
- [ ] Publicar os arquivos do Filament no build da imagem e ignorá-los no Git.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 2: Usuários e alteração de papel

**Arquivos:** `ChangeUserRole.php`, `app/Filament/Resources/Users/`, `backend/tests/Feature/Admin/ManageUsersTest.php`.

**Interfaces:**
- Produz: `ChangeUserRole::handle(User $actor, User $target, Role $role): void`.

- [ ] Escrever `ManageUsersTest`:
  - lista e busca por nome, nome de exibição e e-mail;
  - filtro por papel;
  - conta sem nome de exibição não quebra a tela;
  - altera para os três papéis, sem mudar outros campos;
  - a ação fica escondida na própria linha, e a chamada forjada não muda o papel;
  - `ChangeUserRole` responde 403 ao mudar o próprio papel ou quando quem altera não é ADMINISTRADOR;
  - papel igual não grava nem registra log;
  - o log traz `actor_id`, `target_id`, `from` e `to`, sem e-mail nem nome;
  - a tela não tem criar, editar nem excluir.
- [ ] Ver os testes falharem.
- [ ] Implementar a Action e a tela com slug `usuarios`, rótulo "Usuários" e os textos da spec.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 3: Moderações

**Arquivos:** `app/Filament/Resources/Materials/`, `backend/tests/Feature/Admin/ModerationHistoryTest.php`.

- [ ] Escrever `ModerationHistoryTest`:
  - lista materiais nas situações Pendente, Aprovado, Rejeitado, Cancelado e Excluído, além de alterações;
  - aprovado e depois excluído aparece como Excluído;
  - material anônimo mostra o autor e "Enviado anonimamente";
  - decisor ausente mostra "—";
  - filtro por situação, incluindo Excluído;
  - busca por título e autor;
  - o detalhe mostra descrição, link, motivo e dados originais da alteração;
  - nenhuma ação altera dados, e o material fica igual depois de abrir o detalhe.
- [ ] Ver os testes falharem.
- [ ] Implementar a tela com slug `moderacoes`, rótulo "Moderações", as cores da spec e sem depender dos métodos que a `MaterialPolicy` não tem.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 4: Link na SPA

**Arquivos:** `roles.ts`, `Sidebar.tsx` e, se necessário, `vite.config.ts`.

**Interfaces:**
- Produz: `isAdmin(role: Role): boolean`.

- [ ] Adicionar "Painel administrativo" abaixo de Moderação, só para ADMINISTRADOR, como link comum na mesma aba.
- [ ] Fazer o link funcionar também no ambiente de desenvolvimento.
- [ ] `make lint` verde. Verificar no navegador com ALUNO, MODERADOR e ADMINISTRADOR. Commit.

### Tarefa 5: Verificação de ponta a ponta e documentos

**Arquivos:** os documentos da seção 9 da spec.

- [ ] Script Playwright fora do repositório, em 1440×900 e 390×844:
  - ida e volta entre app e painel, sem novo login;
  - Sair no painel encerra o app;
  - promover e rebaixar uma conta, conferindo o acesso dela;
  - textos em português, validações e página 403;
  - sem rolagem horizontal da página.
- [ ] Construir a imagem de produção e abrir o painel nela, com CSS e JavaScript carregados e registrando o uso de memória.
- [ ] Atualizar os documentos e corrigir a spec no que a implementação mudar.
- [ ] `make test` e `make lint` verdes. Commit.
- [ ] Revisão de toda a branch, correção do que for importante e relatório final.
