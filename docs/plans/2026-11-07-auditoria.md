# Auditoria — plano de implementação

> **Para agentes:** use `superpowers:subagent-driven-development` ou `superpowers:executing-plans` para executar tarefa por tarefa. Os passos usam checkbox (`- [ ]`).

**Objetivo:** gravar um evento imutável para cada ação que muda dados e mostrá-los ao ADMINISTRADOR em `/admin/auditoria`.

**Arquitetura:** uma tabela `audit_events` protegida por trigger. As Actions existentes gravam o evento na mesma transação, por meio de `RecordAuditEvent`. A tela é um Resource do Filament só de leitura.

**Stack:** Laravel 13, PostgreSQL 17, Filament 5, Pest.

**Spec:** `docs/specs/2026-11-07-auditoria-design.md`

## Restrições globais

- Branch `feat/auditoria`. Commits em Conventional Commits, em português, terminando com `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`. Cada commit só acontece depois de os testes e o lint passarem.
- Nenhum evento guarda IP, e-mail, senha, hash, token, sessão ou o texto do motivo.
- Sem mudança na API, então `make api-types` não é necessário.
- Os textos de tela são os das seções 5 e 6 da spec.

## Foco de revisão

1. **Decisão que chega tarde ou envio já cancelado:** não grava evento. Testado na Tarefa 2.
2. **Aprovação de alteração:** `previous` guarda a versão publicada antes, e não a nova. Testado na Tarefa 2.
3. **Cancelamento ou exclusão de material anônimo:** a tela mostra o autor com a marca. Na aprovação, quem fez é quem decidiu, e a marca não aparece abaixo dessa pessoa. Testado na Tarefa 3.
4. **Workflow com papel igual ao atual:** não grava evento. Testado na Tarefa 1.
5. **Filtro por período com só uma das datas preenchida:** funciona. Testado na Tarefa 3.

## Interfaces

- `App\Enums\AuditAction` (string): `user_registered`, `password_changed`, `role_changed`, `material_submitted`, `revision_submitted`, `submission_cancelled`, `material_deleted`, `material_approved`, `material_rejected`, com `label(): string` (seção 6 da spec).
- `App\Models\AuditEvent`: sem `updated_at`. Relações `actor`, `user` e `material` (esta com excluídos).
- `App\Actions\RecordAuditEvent::handle(AuditAction $action, ?User $actor, ?User $user = null, ?Material $material = null, array $data = []): void`. Quando há material, acrescenta `anonymous` e `revision` em `data`.

---

### Tarefa 1: Tabela, gravação e papéis

- [ ] Testes: tabela recusa `UPDATE`, `DELETE` e `TRUNCATE`. `CHECK` de `action`. `actor_id` nulo só em `role_changed`. Painel e workflow gravam `role_changed` com `from`, `to` e `via`. Papel igual não grava.
- [ ] Ver falhar. Implementar migration, enum, model e `RecordAuditEvent`, e usar em `ChangeUserRole` e `PromoteUser`.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 2: Eventos de conta e de materiais

- [ ] Testes: um evento por ação (`RegisterUser`, `ChangePassword`, `CreateMaterial`, `ReviseMaterial`, `CancelMaterial`, `DeleteMaterial`, `ApproveMaterial`, `RejectMaterial`), com quem fez e sobre o quê. Nenhum evento quando a ação não vale. `previous` na aprovação de alteração. JSON de cada tipo sem dados proibidos.
- [ ] Ver falhar. Gravar nas Actions, na mesma transação. `CreateMaterial` e `CancelMaterial` passam a usar uma transação.
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 3: Tela Auditoria

- [ ] Testes: só ADMINISTRADOR. Colunas e textos, "Workflow do GitHub", marca do envio anônimo só sob o autor, filtros por ação, pessoa e período, detalhe com a versão anterior e nenhuma ação que altere dados.
- [ ] Ver falhar. Implementar `AuditEventResource` (slug `auditoria`, terceiro no menu).
- [ ] `make test` e `make lint` verdes. Commit.

### Tarefa 4: Navegador e documentos

- [ ] Playwright: fluxo real (envio anônimo, aprovação, alteração aprovada, troca de papel) e larguras de 320 a 1920 px.
- [ ] Documentos da seção 8 da spec. `make test` e `make lint` verdes. Commit.
