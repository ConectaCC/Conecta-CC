# Oportunidades — plano de implementação

> **Execução:** tarefa por tarefa. Os passos usam checkbox (`- [ ]`) para acompanhamento.

**Objetivo:** página `/oportunidade` com envio moderado, prazo, autoria só interna e integração ao mural da Comunidade.

**Arquitetura:** tabela `opportunities` espelhando `materials`, com Actions, policy, Form Request e Resources próprios, nos mesmos padrões. O mural junta as duas tabelas num `UNION ALL` paginado por cursor. Auditoria, notificações, Meus conteúdos, Moderação e `/admin` ganham o novo tipo.

**Stack:** Laravel 13, PostgreSQL 17, Filament 5, Pest, React 19, TanStack Query.

**Spec:** `docs/specs/2026-11-08-oportunidades-design.md`

## Restrições globais

- Branch `feat/oportunidades`. Commits em Conventional Commits, em português. Cada commit só depois de os testes e o lint passarem.
- Nenhuma resposta pública traz o autor de uma oportunidade.
- Prazo: `deadline`, ou a data de publicação em Brasília mais 15 dias. A oportunidade é visível até o fim desse dia, em Brasília. A data de "hoje" vem do PHP, para os testes conseguirem mudar o relógio.
- Valores: tipos `ESTAGIO`, `EMPREGO`, `EVENTO`, `PROJETO` e `OUTRO`; modalidades `REMOTO`, `HIBRIDO` e `PRESENCIAL`. Os rótulos ficam no front e no `/admin`.
- Código público: `OPO-00001`.
- Toda ação nova grava o evento de auditoria na mesma transação (§66.7).
- Depois de mudar a API, rodar `make api-types`.

## Foco de revisão

1. **Último dia do prazo, às 23h em Brasília (02h UTC do dia seguinte):** a oportunidade ainda aparece. Testado na Tarefa 1.
2. **Oportunidade vencida:** sai da página, do mural e dos números, mas continua em Meus conteúdos como "Encerrada". Testado nas Tarefas 1, 2 e 4.
3. **Mural com material e oportunidade publicados no mesmo instante:** a paginação por cursor não repete nem pula itens. Testado na Tarefa 4.
4. **MODERADOR que enviou a oportunidade:** não a vê na fila e recebe 403 ao tentar decidir. Testado na Tarefa 3.
5. **Aprovação depois do prazo informado:** é aceita, mas a oportunidade já nasce vencida e não aparece. Testado na Tarefa 3.

## Tarefas

1. **Modelo, envio e lista pública:** migration, enums, model, factory, policy, `StoreOpportunityRequest`, `CreateOpportunity`, `GET` e `POST /opportunities` e auditoria do envio.
2. **Autor:** cancelar, excluir e `GET /me/opportunities`, com auditoria.
3. **Moderação:** fila, aprovar e rejeitar, `OpportunityDecided`, textos e links das notificações e auditoria.
4. **Mural e números:** `UNION ALL` com `type`, a busca nos dois tipos e `opportunities` em `stats`.
5. **`/admin`:** página Oportunidades e a auditoria mostrando a oportunidade.
6. **Front da página:** `/oportunidade`, card das bolsas, busca, filtro, cards, modal e sidebar.
7. **Front do restante:** Moderação mista, Meus conteúdos com filtro de tipo, notificações, mural com filtro, card da oportunidade, menu "Compartilhar" e números.
8. **Verificação e documentos:** Playwright e a seção 7 da spec.
