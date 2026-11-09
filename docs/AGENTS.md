# AGENTS.md — docs

Leia antes o [`AGENTS.md`](../AGENTS.md) da raiz. Tudo aqui é escrito em português do Brasil, em frases curtas e diretas. As datas no texto seguem o formato **dd/mm/aaaa**.

## Regras de escrita

A documentação mostra o projeto como ele está hoje e separa claramente o que é futuro.

- **Sem menção a IA.** Nada de "escolha do Claude", "sugestão da IA", "gerado por IA". A documentação é escrita e mantida pelo time.
- **Confira antes de afirmar.** Antes de escrever que algo existe, não existe, é futuro ou está em aberto, confira nesta ordem: código na `main`, decisões mais recentes, specs mais recentes, documentação existente. Texto antigo não é verdade só por estar no arquivo.
- **Sem contradição.** Antes de registrar uma decisão, procure o assunto nos outros documentos e ajuste o que ficou errado. Não acrescente no fim deixando informação errada antes.
- **Pendência resolvida sai.** Revise "em aberto", "a definir", "será decidido", "será avaliado" e "próxima atividade" sobre o assunto decidido.
- **Atual × futuro.** O que não existe aparece como evolução futura, pós-MVP ou comportamento previsto. O que existe não aparece como futuro.
- **Termo comum não vira entidade.** "Publicação" no sentido de publicar não é uma entidade `PUBLICAÇÃO`, a menos que ela exista no sistema.
- **Nada institucional inventado:** URL, contato, telefone, domínio ou comportamento de serviço externo. Sem confirmação, fica pendente ou não aparece. Quando for confirmado, remova o texto antigo.
- **Decisão de produto é do time.** Uma situação sem regra que afeta o produto vira pergunta, não decisão inventada.
- **Nomes do projeto:** ALUNO, MODERADOR, ADMINISTRADOR, Estudos, Oportunidade, Comunidade, Perfil, Meus conteúdos, Moderação.
- **Texto natural e enxuto.** Registre o que foi decidido, a regra, o comportamento e a justificativa quando importa. Sem raciocínio interno, sem quem sugeriu cada detalhe, sem alternativas irrelevantes, sem repetir a mesma justificativa em várias seções.

Antes de subir, procure no que mudou: "Claude", "IA", "escolha do", "sugestão do", "em aberto", "a definir", "será definido", "ainda será", "futuramente", "piloto", "fora do MVP", "substituída" e "atualização". Cada ocorrência precisa continuar verdadeira, e nenhuma seção anterior pode ter ficado obsoleta.

## Mapa dos documentos

| Arquivo | O que guarda | Como atualizar |
|---|---|---|
| `mvp.md` | escopo do MVP | só com decisão do time |
| `requisitos.md` | requisitos numerados (RF-xx) | acrescentar ou ajustar o RF para a regra vigente, sem manter a versão antiga ao lado |
| `fluxos.md` | fluxos das telas | seguir o formato `Tela → ação → resultado` |
| `decisoes.md` | registro numerado de decisões | **acrescentar** uma seção nova no fim (ver abaixo) e ajustar as seções antigas que ela contradiz |
| `pendencias.md` | questões em aberto | só o que ainda está em aberto. Item decidido sai daqui (a decisão fica em `decisoes.md`) |
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

Quando uma decisão nova muda uma antiga, ajuste a seção antiga para não deixar duas regras ativas ao mesmo tempo. Não deixe a regra antiga, um aviso de que foi substituída e a regra nova lado a lado: fica só a regra vigente, salvo quando o histórico é necessário para entender a decisão.

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

Uma escolha técnica que o time não fez é registrada como escolha da implementação. Se ela afeta o produto (UX, regra de negócio, permissões, privacidade, visibilidade, prazos, fluxo, escopo), fica como pergunta para o time. Se a implementação mudar algo da spec aprovada, a spec é corrigida no mesmo PR, com o motivo.

## Plano — `plans/AAAA-MM-DD-<tema>.md`

1. Cabeçalho para agentes, com **Objetivo**, **Arquitetura**, **Stack** e **Spec**.
2. **Restrições globais:** branch, formato dos commits, o que não entra e **todos os textos exatos** da tela.
3. **Foco de revisão:** as 5 entradas que a spec não cita mas que vão acontecer (busca só com espaços, URL com maiúsculas, recarregar a página, nome longo no celular…). Cada uma ligada à tarefa que a testa.
4. **Mapa de arquivos.**
5. **Tarefas** (`### Tarefa N`), cada uma com **Arquivos**, **Interfaces** (o que consome e o que produz, com assinaturas exatas) e passos em checkbox: teste que falha, ver falhar, implementar, ver passar, commit.
6. **Verificação final:** `make lint`, `make mutate` e `make mutate-front` verdes antes do PR, e `make e2e` quando o fluxo de tela muda (`stack-e-decisoes-tecnicas.md` 12).

O plano decide nomes, assinaturas, valores e testes. O corpo do código fica para quem implementa. Um plano maior que o código que descreve passou do ponto.
