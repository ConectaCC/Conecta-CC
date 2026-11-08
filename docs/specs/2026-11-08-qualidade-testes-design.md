# Qualidade dos testes e análise estática — design

Data: 08/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #18)

Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Os testes provam que o código faz o que o teste confere, mas não que o teste confere o que importa. Uma rodada de amostra mostrou isso: os testes de `ChangeUserRole` aceitam qualquer erro HTTP onde a regra exige 403.

O **teste de mutação** altera o código de propósito (troca `>` por `>=`, `403` por `404`, apaga uma linha) e verifica se algum teste falha. Quando nenhum falha, há uma lacuna.

A **análise estática pesada** pega, sem rodar nada, erros de tipo que os testes podem não alcançar.

## 2. Decisões (08/11/2026)

1. **Rodada completa de mutação no back:** em todas as classes de `backend/app`, com relatório antes das correções. As lacunas reais ganham testes. Mutantes que não mudam o comportamento são ignorados com um comentário justificando.
2. **Análise estática no back:** o Larastan sobe do nível 6 para o 10 (máximo), com todos os erros corrigidos e sem lista de exceções.
3. **Fluxo das próximas entregas:** o teste de mutação roda só no que for novo, ou seja, nas classes e arquivos alterados em relação à `main`.
   - **Nota mínima:** 90% no back e 80% no front.
   - **Execução:** passo no CI e comando local antes do PR.
4. **Análise estática no front:** `strict` e `noUncheckedIndexedAccess` no TypeScript, e as regras do ESLint que usam tipos (`strictTypeChecked`). `exactOptionalPropertyTypes` fica de fora, porque gera muitos erros de biblioteca e quase não pega problema real **(escolha do Claude)**.
5. **Testes do front:** Vitest e Testing Library, como dependências só de desenvolvimento, começando pela lógica mais sensível.
   - **Lógica:** datas e prazos, busca e filtros no endereço, rolagem até o item e iniciais.
   - **Regras de tela de privacidade:** o material anônimo e a oportunidade nunca mostram quem enviou.
   - **Formulários:** as validações.
6. **Teste de mutação no front:** StrykerJS com Vitest, sem mutar textos fixos (classes CSS e textos de tela), que só gerariam mutantes sem efeito real.
7. **Ponta a ponta:** os scripts Playwright vão para o repositório como `make e2e`, só no ambiente local. Eles não entram no CI por enquanto.
8. **Ordem:** primeiro o back, depois o front, em entregas separadas.

## 3. Back

### Comandos

| Comando | O que faz |
|---|---|
| `make mutate` | teste de mutação nas classes de `backend/app` alteradas em relação à `main`, com nota mínima de 90% |
| `make lint` | já existente; passa a rodar o Larastan no nível 10 |

### CI

O job `backend` liga a cobertura (PCOV) e ganha um passo que roda `make mutate`, ou o equivalente, só quando o PR altera `backend/app`.

### Limite conhecido

O Pest muta por classe, e não por linha. Mudar uma linha numa classe grande testa a classe inteira. Neste projeto, as classes são pequenas.

## 4. Front

### Comandos

| Comando | O que faz |
|---|---|
| `make test-front` | testes do front (Vitest) |
| `make mutate-front` | StrykerJS nos arquivos de `frontend/src` alterados em relação à `main`, com nota mínima de 80% |
| `make e2e` | scripts Playwright de ponta a ponta, com o ambiente local de pé |

### CI

O job `frontend` passa a rodar os testes e, quando o PR altera `frontend/src`, o StrykerJS nos arquivos alterados.

### Fora do teste de mutação

- `src/components/ui`, gerado pelo shadcn;
- `src/lib/api/schema.d.ts`, gerado a partir da API.

## 5. Documentação a atualizar

- **`AGENTS.md` da raiz:** "Como uma entrega é feita" ganha o teste de mutação no que for novo e a nota mínima.
- **`backend/AGENTS.md` e `frontend/AGENTS.md`:** comandos e regras.
- **`docs/AGENTS.md`:** o plano de cada entrega passa a prever o passo de mutação.
- **`stack-e-decisoes-tecnicas.md`:** nova decisão técnica, no formato Decisão, Motivo, Alternativas descartadas e Consequência.
- **`decisoes.md`:** seção com as decisões da seção 2.
- **`operacao.md`:** os novos comandos.

## 6. Fica para o builder

- Como descobrir as classes alteradas (lista de arquivos do `git diff` convertida em nomes de classe).
- Como tratar um PR que só apaga código (nenhuma classe para mutar).
- A configuração exata do StrykerJS e do Vitest.
