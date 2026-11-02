# ConectaCC

> **Conexão, conhecimento, oportunidades e comunidade.**

O **ConectaCC** é uma plataforma acadêmica e colaborativa criada inicialmente para estudantes do curso de **Ciência da Computação do IFSul – Câmpus Passo Fundo**.

O projeto surgiu no contexto da disciplina de **Práticas Curriculares em Sociedade III (PCS III)** e busca facilitar a experiência acadêmica por meio da conexão entre estudantes, conhecimentos, materiais, oportunidades e recursos que já fazem parte do ecossistema institucional.

---

## Sobre o projeto

O ConectaCC parte de um princípio central:

> **Conectar o que já existe e criar novas formas de colaboração entre estudantes.**

A proposta não é substituir ferramentas como:

- Moodle;
- SUAP;
- páginas institucionais;
- sites do IFSul;
- sistemas acadêmicos existentes.

O ConectaCC pretende funcionar como uma camada de **conexão, organização e comunidade**, ajudando o estudante a:

- encontrar informações;
- acessar recursos acadêmicos;
- compartilhar conhecimento;
- descobrir oportunidades;
- conhecer outros estudantes;
- fortalecer a comunidade do curso.

---

## Conceitos centrais

O projeto é orientado por quatro conceitos:

### Conexão

Aproximar estudantes, conhecimentos, recursos e iniciativas.

### Conhecimento

Facilitar o acesso e o compartilhamento de materiais acadêmicos.

### Oportunidades

Aproximar estudantes de estágios, projetos, eventos e outras possibilidades de desenvolvimento.

### Comunidade

Fortalecer colaboração, integração e apoio entre estudantes.

---

## Público inicial

O MVP é voltado inicialmente para estudantes de:

**Ciência da Computação — IFSul Câmpus Passo Fundo**

A expansão para outros cursos ou câmpus poderá ser estudada posteriormente.

---

## MVP

O MVP atual contempla:

- Login;
- Cadastro;
- Termos de participação;
- Recuperação de senha;
- Home;
- Perfil;
- Estudos;
- Oportunidade;
- Comunidade;
- Perfil público;
- Configurações;
- Notificações;
- Moderação para MODERADOR e ADMINISTRADOR.

O objetivo do MVP é validar a utilidade do ConectaCC antes da inclusão de funcionalidades mais complexas.

---

## Navegação principal

### ALUNO

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil

Na parte inferior:

- Configurações
- Sair

### MODERADOR e ADMINISTRADOR

Possuem as mesmas áreas e também acesso à Moderação, dentro da aplicação comum.

O painel administrativo em `/admin` é exclusivo do ADMINISTRADOR.

---

## Protótipo

O protótipo navegável do MVP está disponível em:

https://claude.ai/artifact/Gwv1arQoKYix47kJHF3dF4

As dez áreas principais do MVP foram prototipadas e congeladas.

Foi realizada também uma auditoria geral do protótipo para verificar:

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
- consistência de escopo.

> **Uma funcionalidade prototipada não deve ser considerada automaticamente implementada.**

---

## Status atual

O projeto encontra-se na etapa de:

**definição concluída do MVP + prototipação concluída + preparação para implementação.**

Atualmente:

- ✅ escopo do MVP definido;
- ✅ identidade visual definida;
- ✅ principais fluxos definidos;
- ✅ requisitos documentados;
- ✅ dez áreas do MVP prototipadas;
- ✅ auditoria geral do protótipo realizada;
- ✅ arquitetura técnica definida;
- 🚧 implementação em andamento;
- ⏳ testes técnicos;
- ⏳ preparação do piloto;
- ⏳ validação com estudantes.

---

## Documentação

A documentação do projeto está organizada em:

### Produto

- [`docs/mvp.md`](docs/mvp.md) — escopo do MVP;
- [`docs/status-mvp.md`](docs/status-mvp.md) — estado atual do projeto;
- [`docs/requisitos.md`](docs/requisitos.md) — requisitos funcionais e regras;
- [`docs/fluxos.md`](docs/fluxos.md) — principais fluxos do sistema;
- [`docs/personas.md`](docs/personas.md) — personas preliminares.

### Planejamento

- [`docs/roadmap.md`](docs/roadmap.md) — evolução planejada;
- [`docs/pendencias.md`](docs/pendencias.md) — decisões e questões ainda abertas;
- [`docs/implementacao.md`](docs/implementacao.md) — etapas necessárias para implementação;
- [`docs/decisoes.md`](docs/decisoes.md) — registro das decisões do projeto.

### Design e ecossistema

- [`docs/identidade-visual.md`](docs/identidade-visual.md) — identidade visual;
- [`docs/recursos-institucionais.md`](docs/recursos-institucionais.md) — recursos, sistemas e links relevantes;
- [`design/README.md`](design/README.md) — informações sobre o protótipo e o design.

---

## Estado da implementação

Fundação técnica, Cadastro, Login e Home implementados (ver [`docs/specs/2026-10-12-fundacao-design.md`](docs/specs/2026-10-12-fundacao-design.md)). Estudos implementado na etapa 1, só leitura (ver [`docs/specs/2026-10-31-estudos-design.md`](docs/specs/2026-10-31-estudos-design.md)). Compartilhamento de materiais (identificado ou anônimo) e Moderação de material implementados na etapa 2a, e Meus conteúdos com editar, excluir e cancelar na etapa 2b (ver [`docs/specs/2026-11-02-estudos-parte-2-design.md`](docs/specs/2026-11-02-estudos-parte-2-design.md)).

As decisões de stack e arquitetura estão em [`docs/stack-e-decisoes-tecnicas.md`](docs/stack-e-decisoes-tecnicas.md). Elas são conduzidas principalmente por **Marcelo Henrique Germiniani Panho**, com revisão da equipe para garantir aderência aos requisitos definidos.

---

## Desenvolvimento local

Requer Docker. Rode `make setup` na primeira vez e acesse http://localhost:5173. Detalhes em [`docs/operacao.md`](docs/operacao.md).

---

## Desenvolvimento

Projeto desenvolvido por:

**Viviane Biazus Aneris**  
**Marcelo Henrique Germiniani Panho**

Curso de Ciência da Computação  
**IFSul – Câmpus Passo Fundo**

---

## Evolução possível

O ConectaCC foi pensado para permitir evolução gradual.

Possíveis etapas:

1. definição e prototipação;
2. arquitetura e implementação do MVP;
3. testes internos;
4. disciplinas piloto;
5. piloto com estudantes;
6. revisão com base em feedback;
7. possível continuidade institucional;
8. possível projeto de extensão;
9. possível projeto de pesquisa;
10. possível produção científica.

Essas possibilidades dependem dos resultados do projeto, da validação com usuários e das orientações institucionais.

---

## Princípio do projeto

> **Feito por alunos, para alunos.**

O objetivo não é desenvolver o maior número possível de funcionalidades.

O objetivo é construir algo útil, sustentável e coerente com as necessidades reais da comunidade acadêmica.
