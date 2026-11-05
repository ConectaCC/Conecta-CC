# Status do MVP — ConectaCC

Última atualização: 29/09/2026

Este documento apresenta o estado atual do MVP do ConectaCC.

A intenção é diferenciar claramente o que já foi definido e prototipado do que ainda precisa ser implementado e validado.

---

## Legenda

- ✅ **Definido** — requisitos e regras principais já foram estabelecidos.
- 🎨 **Prototipado** — existe representação visual e fluxo navegável.
- 💻 **Implementado** — existe código funcional.
- 🧪 **Validado** — foi submetido ao processo de teste ou validação previsto.
- ⏳ **Pendente** — ainda precisa ser realizado.

---

## Visão geral

| Funcionalidade | Definido | Prototipado | Implementado | Validado |
|---|---|---|---|---|
| Identidade visual | ✅ | ✅ | ✅ | ⏳ |
| Login | ✅ | ✅ | ✅ | ⏳ |
| Cadastro | ✅ | ✅ | ✅ | ⏳ |
| Termos de participação | ✅ | ✅ | ✅ | ⏳ |
| Recuperação de senha | ✅ | ✅ | ⏳ (opcional — decisoes.md 52.9) | ⏳ |
| Home | ✅ | ✅ | ✅ | ⏳ |
| Perfil | ✅ | ✅ | ✅ | ⏳ |
| Estudos | ✅ | ✅ | ✅ | ⏳ |
| Página da disciplina | ✅ | ✅ | ✅ | ⏳ |
| Compartilhamento de material | ✅ | ✅ | ✅ (2a) | ⏳ |
| Oportunidade | ✅ | ✅ | fora do MVP | — |
| Indicação de oportunidade | ✅ | ✅ | fora do MVP | — |
| Comunidade | ✅ | ✅ | fora do MVP | — |
| Perfil público | ✅ | ✅ | ✅ | ⏳ |
| Meus conteúdos (materiais) | ✅ | ✅ (esboço) | ✅ (2b) | ⏳ |
| Compartilhamento na Comunidade | ✅ | ✅ | fora do MVP | — |
| Busca de estudantes | ✅ | ✅ | fora do MVP | — |
| Configurações | ✅ | ✅ | ✅ | ⏳ |
| Notificações | ✅ | ✅ | ✅ (material) | ⏳ |
| Moderação ADMIN | ✅ | ✅ | ✅ (só material) | ⏳ |
| Responsividade | ✅ | ✅ | ⏳ | ⏳ |
| Acessibilidade | ✅ | Considerada no protótipo | ⏳ | ⏳ |
| Testes com estudantes | Planejado | — | — | ⏳ |

---

# Situação atual

> **Atualização (02/11/2026):** a implementação está em andamento, e o escopo do MVP foi reduzido: Oportunidade e Comunidade ficaram para a próxima versão (`decisoes.md` 56).

**Já implementado e publicado** (PRs #2 a #7):

- fundação técnica (API Laravel, SPA React, CI, deploy no Render);
- Cadastro, Login, Termos de participação (versão 0.2) e Home;
- Perfil, com disciplinas atuais e o card "Complete seu perfil";
- Estudos: catálogo da Matriz 2023, busca, página da disciplina e ementa oficial;
- materiais por link, identificados ou anônimos, com Moderação de material;
- Meus conteúdos: editar, excluir e cancelar envio;
- notificações de material (sino, contador e painel);
- Perfil público, aberto pelo nome do autor nos materiais.

**Ainda falta para o MVP:** ver a seção "O que ainda não está implementado".

O protótipo das dez áreas continua sendo a referência visual. Ele inclui Oportunidade e Comunidade, que agora estão fora do MVP.

---

# Protótipo

Protótipo navegável:

https://claude.ai/artifact/Gwv1arQoKYix47kJHF3dF4

Após o congelamento das dez áreas, foi realizada uma auditoria geral do protótipo para verificar:

- navegação;
- fluxos;
- terminologia;
- componentes;
- estados de erro;
- loading;
- estados vazios;
- links externos;
- responsividade;
- acessibilidade;
- consistência do escopo.

A auditoria foi revisada pela equipe e suas decisões e pendências foram incorporadas à documentação do projeto.

O parecer final considerou o protótipo consistente o suficiente para servir como referência de implementação.

Essa limpeza não altera o escopo nem os fluxos do MVP.
---

# O que já está definido

Atualmente estão definidos, entre outros:

- objetivo do MVP;
- público inicial;
- papéis ALUNO e ADMIN;
- navegação principal;
- critérios de Perfil completo;
- fluxo de autenticação;
- fluxo de recuperação de senha;
- uso exclusivo de e-mail institucional do IFSul no MVP e no piloto;
- ausência de confirmação de cadastro por link ou código no MVP e no piloto;
- funcionamento de Estudos;
- Matriz 2023 como referência acadêmica inicial;
- organização acadêmica inicial do 1º ao 8º semestre;
- compartilhamento de materiais por link;
- funcionamento de Oportunidade;
- funcionamento da Comunidade;
- busca de estudantes;
- Perfil público;
- Configurações;
- notificações;
- fluxo de Moderação;
- estados PENDENTE, APROVADO e REJEITADO;
- regra de que ADMIN não modera o próprio conteúdo;
- data pública de Material, Publicação e Oportunidade baseada na aprovação/publicação;
- anonimato público dos materiais mantido no MVP atual;
- principais estados de erro, loading e vazio;
- princípios de responsividade e acessibilidade;
- identidade visual;
- escopo de funcionalidades que não pertencem ao MVP.
---

# O que ainda não está implementado

> **Atualização (02/11/2026):** lista revista depois das entregas de Estudos (partes 1 e 2) e da saída de Oportunidade e Comunidade do MVP.

**Funcionalidades do MVP:**

- ~~**Configurações:** e-mail só para leitura e troca de senha, incluindo a política das outras sessões depois da troca.~~ Entregue em 04/11/2026 (`decisoes.md` 61).
- **Painel `/admin` (Filament):** papéis, consulta de moderações e atendimento de exclusão de conta. Ele é necessário porque o Render não tem terminal.
- **Recuperação de senha:** opcional (`decisoes.md` 52.9).

**Operação:**

- backup semanal do banco (`stack-e-decisoes-tecnicas.md`, decisão 10), que ainda não tem workflow;
- monitor externo do `/up` e Sentry, com a configuração conferida no Render;
- `TERMS_VERSION` em `0.2` no Render.

**Antes do piloto** (`pendencias.md` §3):

- revisar os Termos (dados, finalidade, disciplinas visíveis);
- política de exclusão de conta e dados;
- definir quem modera (mais de um MODERADOR, porque o MODERADOR não analisa o próprio envio);
- orientação para escrever o motivo da rejeição;
- levantamento dos recursos institucionais ainda pendentes.

**Qualidade:** uma rodada de testes ponta a ponta de todos os módulos, cobrindo responsividade, acessibilidade, erros e concorrência (`implementacao.md` §19 a §21).

---

# Próximos marcos

## 1. Checkpoint com a professora

O próximo marco do projeto é apresentar:

- problema;
- proposta;
- escopo do MVP;
- protótipo;
- documentação;
- decisões tomadas;
- situação atual;
- próximos passos.

O objetivo é validar a direção adotada antes da criação da arquitetura técnica definitiva.

Caso sejam solicitadas alterações relevantes:

→ revisar produto e documentação  
→ ajustar o protótipo quando necessário  
→ somente depois seguir para a arquitetura.

---

## 2. Definição da arquitetura técnica

> **Atualização (02/11/2026):** concluída. Ver `stack-e-decisoes-tecnicas.md`.

Após o checkpoint e eventuais ajustes, a equipe deverá definir como o sistema será construído.

Entre as principais decisões estão:

1. stack;
2. arquitetura geral;
3. frontend;
4. backend;
5. banco de dados;
6. autenticação;
7. autorização ALUNO/ADMIN;
8. recuperação de senha e envio de e-mail;
9. estrutura das entidades;
10. estratégia de Moderação;
11. estratégia de notificações;
12. segurança;
13. testes;
14. hospedagem e deploy;
15. organização definitiva do código no repositório;
16. estratégia de branches e commits.

Essa etapa será conduzida principalmente por **Marcelo Henrique Germiniani Panho**, com revisão da equipe para garantir aderência aos requisitos definidos.

A pergunta central dessa fase será: > **Como vamos construir o produto que já foi definido?**

---

## 3. Implementação

Somente depois da definição da arquitetura será iniciada a implementação funcional do MVP.

> **Atualização (02/11/2026):** em andamento. Já foram entregues a fundação, a autenticação, o Perfil, a Home, Estudos (partes 1 e 2), Moderação de material, Meus conteúdos, Notificações e Perfil público. Faltam Configurações e o painel `/admin`.

> **Atualização (04/11/2026):** Configurações foi entregue. Falta o painel `/admin`.

---

# Próximas atividades de produto

Após o checkpoint com a professora, permanecem atividades como:

- concluir o levantamento dos recursos institucionais ainda pendentes;
- decidir onde Informações e Serviços aparecerão no ConectaCC;
- confirmar tecnicamente o domínio institucional utilizado no cadastro;
- ~~definir disciplinas piloto~~: todas as disciplinas têm área de materiais (`decisoes.md` 54.2);
- verificar posteriormente se será necessário contemplar estudantes vinculados à Matriz 2017;
- revisar os Termos antes do piloto;
- definir política de exclusão e retenção de dados;
- preparar instrumento de feedback;
- selecionar participantes;
- definir administradores do piloto;
- realizar testes de usabilidade;
- analisar resultados;
- priorizar melhorias.

> **Atualização (02/11/2026):** a autoria foi decidida antes do previsto. O material mostra o nome do autor, e o autor pode escolher compartilhar anonimamente (`decisoes.md` 55.2).

# Decisões futuras que não bloqueiam o projeto

Algumas decisões foram propositalmente adiadas para o momento em que realmente serão necessárias.

## Antes da implementação de Notificações

> **Atualização (02/11/2026):** resolvido. Clicar marca como lida e leva ao conteúdo: uma aprovação abre a disciplina, e uma rejeição abre o item em Meus conteúdos (`decisoes.md` 55.5).

Definir:

- comportamento ao clicar em uma notificação;
- se o clique marca automaticamente como lida;
- para qual conteúdo ou página cada tipo direciona.

## Após o checkpoint com a professora

Retomar:

- organização de Informações e Serviços;
- levantamento dos links institucionais ainda pendentes.

## Próximo semestre

> **Atualização (02/11/2026):** resolvido antes do previsto (`decisoes.md` 55.2).

Avaliar, após os primeiros testes:

- se materiais continuam aparecendo como **Compartilhado pela comunidade**;
- se a autoria deverá passar a ser apresentada publicamente.

## Evoluções futuras

Poderão ser avaliados:

- confirmação de propriedade do e-mail por link ou código;
- suporte a e-mails institucionais de outras instituições;
- expansão para outros cursos, câmpus ou instituições.


---

# Importante

Os termos abaixo não são equivalentes:

**Definido**  
A equipe já decidiu como a funcionalidade deve funcionar.

**Prototipado**  
Existe representação navegável ou visual do comportamento esperado.

**Implementado**  
Existe código funcional executando esse comportamento.

**Validado**  
A funcionalidade foi testada no processo de validação previsto.

Essa distinção deve ser preservada na apresentação e na documentação do ConectaCC.
