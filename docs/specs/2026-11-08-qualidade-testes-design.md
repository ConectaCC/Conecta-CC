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
| `make mutate` | cobertura de linhas de 100% e teste de mutação nas classes de `backend/app` alteradas em relação à `origin/main`, com nota mínima de 90%. Outra base: `make mutate BASE=...` |
| `make lint` | já existente; passa a rodar o Larastan no nível 10 |

### CI

O job `backend` liga a cobertura (PCOV), baixa o histórico completo do Git e roda os testes com `--coverage --min=100`. Em PR, roda a mutação nas classes alteradas em relação à base do PR.

### Como ficou (08/11/2026)

- **Classes alteradas:** `scripts/changed-php-classes.sh` lista os arquivos de `backend/app` criados ou alterados desde o ponto em que a branch saiu da base, incluindo o que ainda não foi commitado, e converte em nomes de classe. O mesmo script serve ao `make mutate` e ao CI.
- **PR que só apaga código:** a lista fica vazia, e o passo de mutação termina sem erro.
- **`--covered-only`:** o Pest trata atributos e propriedades de classe (`#[Fillable]`, `$timestamps`) como linhas não executadas e os conta como mutantes sem teste, mesmo testados. Por isso a mutação roda com `--covered-only`, e a cobertura de linhas de 100% garante que código novo sem teste não passe **(escolha do Claude)**.
- **Marca de mutante equivalente:** `// @pest-mutate-ignore: Mutador1,Mutador2` no fim da linha, com o motivo na linha de cima. Texto depois dos dois-pontos é lido como nome de mutador.
- **`preventLazyLoading`** ligado fora de produção, para que um N+1 em lista falhe no teste **(escolha do Claude)**.

### Resultado da rodada completa (08/11/2026)

| | Antes | Depois |
|---|---|---|
| Nota de mutação (`backend/app`, 93 arquivos) | 75,8% | 100% (1.402 mutantes, nenhum sobrevivente) |
| Cobertura de linhas | — | 100% |
| Larastan | nível 6 | nível 10, sem erros |
| Testes | 422 | 502 |

A rodada completa leva cerca de 11 minutos: `docker compose exec -u sail app vendor/bin/pest --mutate --everything --covered-only --parallel`.

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
