# AGENTS.md — docs

Leia antes o [`AGENTS.md`](../AGENTS.md) da raiz. Tudo aqui é escrito em português do Brasil, em frases curtas e diretas. As datas no texto seguem o formato **dd/mm/aaaa**.

## Mapa dos documentos

| Arquivo | O que guarda | Como atualizar |
|---|---|---|
| `mvp.md` | escopo do MVP | só com decisão do time |
| `requisitos.md` | requisitos numerados (RF-xx) | acrescentar ou ajustar o RF. Mudanças relevantes recebem uma nota de atualização com data |
| `fluxos.md` | fluxos das telas | seguir o formato `Tela → ação → resultado` |
| `decisoes.md` | registro numerado de decisões | **acrescentar** uma seção nova no fim (ver abaixo). Nunca reescrever decisão antiga |
| `pendencias.md` | questões em aberto | não apagar. Acrescentar `> **Atualização (dd/mm/aaaa):** ...` no item resolvido ou alterado |
| `implementacao.md` | checklists de implementação por área e a ordem de implementação (§22) | marcar `- [x]` no que foi entregue e ajustar a ordem quando o plano mudar |
| `status-mvp.md` | tabela Definido / Prototipado / Implementado / Validado | atualizar as linhas da área entregue |
| `roadmap.md` | evolução planejada | — |
| `stack-e-decisoes-tecnicas.md` | decisões técnicas no formato Decisão / Motivo / Alternativas descartadas / Consequência | nova decisão técnica entra numerada nesse formato |
| `operacao.md` | como subir, testar e operar a produção | toda mudança em comando, variável de ambiente ou procedimento |
| `identidade-visual.md`, `personas.md`, `recursos-institucionais.md` | referência de produto e design | — |
| `specs/` | design aprovado de cada entrega | um arquivo por entrega |
| `plans/` | plano de implementação de cada entrega | um arquivo por entrega |

Além disso, a seção "Estado da implementação" do `README.md` da raiz lista o que já foi entregue.

## `decisoes.md`

Cada rodada de decisões ganha uma seção numerada em sequência:

```markdown
# 54. Estudos (etapa 1) — 31/10/2026

Decisões tomadas na definição de Estudos (spec `specs/2026-10-31-estudos-design.md`).

## 54.1 Duas etapas
...
```

Quando uma decisão nova substitui uma antiga, diga isso explicitamente (por exemplo: "Isso substitui, para materiais, o item §19"). Não edite o texto antigo.

## Spec de entrega — `specs/AAAA-MM-DD-<tema>-design.md`

A spec é escrita depois das perguntas ao time e **aprovada antes de qualquer código**. A estrutura usada até agora:

1. Cabeçalho com **Data**, **Responsável**, **Base** (branch ou PR de partida) e referência visual (artboards do protótipo).
2. **Intenção:** o que a entrega resolve e por quê.
3. **Escopo:** **Entra** e **Não entra** (com o motivo de cada exclusão).
4. **Decisões do time,** com data.
5. **Backend** e **Frontend:** contratos, rotas, textos exatos e estados de tela.
6. **Testes:** o que prova cada comportamento.
7. **Documentação a atualizar** junto com o código.
8. **Fica para o builder:** o que pode ser decidido na implementação.

Toda escolha que o time não fez leva a marca **(escolha do Claude)**. Se a implementação mudar algo da spec aprovada, a spec é corrigida no mesmo PR, com o motivo.

## Plano — `plans/AAAA-MM-DD-<tema>.md`

1. Cabeçalho para agentes, com **Objetivo**, **Arquitetura**, **Stack** e **Spec**.
2. **Restrições globais:** branch, formato dos commits, o que não entra e **todos os textos exatos** da tela.
3. **Foco de revisão:** as 5 entradas que a spec não cita mas que vão acontecer (busca só com espaços, URL com maiúsculas, recarregar a página, nome longo no celular…). Cada uma ligada à tarefa que a testa.
4. **Mapa de arquivos.**
5. **Tarefas** (`### Tarefa N`), cada uma com **Arquivos**, **Interfaces** (o que consome e o que produz, com assinaturas exatas) e passos em checkbox: teste que falha, ver falhar, implementar, ver passar, commit.
6. **Verificação final:** `make lint`, `make mutate` e `make mutate-front` verdes antes do PR, e `make e2e` quando o fluxo de tela muda (`stack-e-decisoes-tecnicas.md` 12).

O plano decide nomes, assinaturas, valores e testes. O corpo do código fica para quem implementa. Um plano maior que o código que descreve passou do ponto.
