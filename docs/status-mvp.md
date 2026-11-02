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
| Identidade visual | ✅ | ✅ | ⏳ | ⏳ |
| Login | ✅ | ✅ | ⏳ | ⏳ |
| Cadastro | ✅ | ✅ | ⏳ | ⏳ |
| Termos de participação | ✅ | ✅ | ⏳ | ⏳ |
| Recuperação de senha | ✅ | ✅ | ⏳ | ⏳ |
| Home | ✅ | ✅ | ⏳ | ⏳ |
| Perfil | ✅ | ✅ | ⏳ | ⏳ |
| Estudos | ✅ | ✅ | ✅ (leitura) | ⏳ |
| Página da disciplina | ✅ | ✅ | ✅ (sem materiais) | ⏳ |
| Compartilhamento de material | ✅ | ✅ | ✅ (2a) | ⏳ |
| Oportunidade | ✅ | ✅ | ⏳ | ⏳ |
| Indicação de oportunidade | ✅ | ✅ | ⏳ | ⏳ |
| Comunidade | ✅ | ✅ | ⏳ | ⏳ |
| Perfil público | ✅ | ✅ | ⏳ | ⏳ |
| Compartilhamento na Comunidade | ✅ | ✅ | ⏳ | ⏳ |
| Busca de estudantes | ✅ | ✅ | ⏳ | ⏳ |
| Configurações | ✅ | ✅ | ⏳ | ⏳ |
| Notificações | ✅ | ✅ | ⏳ | ⏳ |
| Moderação ADMIN | ✅ | ✅ | ✅ (só material) | ⏳ |
| Responsividade | ✅ | ✅ | ⏳ | ⏳ |
| Acessibilidade | ✅ | Considerada no protótipo | ⏳ | ⏳ |
| Testes com estudantes | Planejado | — | — | ⏳ |

---

# Situação atual

As dez áreas principais do MVP já foram prototipadas e congeladas:

1. Login
2. Cadastro + Termos
3. Recuperação de senha
4. Home
5. Perfil
6. Estudos
7. Oportunidade
8. Comunidade + Perfil público
9. Configurações
10. Moderação — ADMIN

O protótipo representa a especificação visual e funcional planejada para o MVP.

Isso **não significa que o sistema já esteja implementado em código**.

A etapa de:

**Produto + UX + documentação do MVP**

está considerada concluída para o checkpoint atual.

O próximo marco será a apresentação do projeto à professora.

Após esse checkpoint:

- eventuais ajustes solicitados serão avaliados;
- a arquitetura técnica será definida;
- somente depois será iniciada a implementação em código.

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

Ainda precisam ser transformados em código:

- autenticação real;
- banco de dados;
- Cadastro;
- Login;
- recuperação de senha;
- papéis e autorização;
- Home;
- Perfil;
- Estudos;
- Oportunidade;
- Comunidade;
- Perfil público;
- Configurações;
- notificações;
- Moderação;
- integração entre os módulos;
- persistência dos dados;
- responsividade real;
- testes automatizados e manuais.

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

---

# Próximas atividades de produto

Após o checkpoint com a professora, permanecem atividades como:

- concluir o levantamento dos recursos institucionais ainda pendentes;
- decidir onde Informações e Serviços aparecerão no ConectaCC;
- confirmar tecnicamente o domínio institucional utilizado no cadastro;
- definir disciplinas piloto;
- verificar posteriormente se será necessário contemplar estudantes vinculados à Matriz 2017;
- revisar os Termos antes do piloto;
- definir política de exclusão e retenção de dados;
- preparar instrumento de feedback;
- selecionar participantes;
- definir administradores do piloto;
- realizar testes de usabilidade;
- analisar resultados;
- priorizar melhorias.

A decisão sobre apresentar ou não publicamente a autoria dos materiais será retomada apenas no próximo semestre, após os primeiros testes com estudantes.

# Decisões futuras que não bloqueiam o projeto

Algumas decisões foram propositalmente adiadas para o momento em que realmente serão necessárias.

## Antes da implementação de Notificações

Definir:

- comportamento ao clicar em uma notificação;
- se o clique marca automaticamente como lida;
- para qual conteúdo ou página cada tipo direciona.

## Após o checkpoint com a professora

Retomar:

- organização de Informações e Serviços;
- levantamento dos links institucionais ainda pendentes.

## Próximo semestre

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
