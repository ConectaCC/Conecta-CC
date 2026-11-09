# Roadmap - ConectaCC

Este documento apresenta uma proposta de evolução gradual do ConectaCC.

O roadmap não representa um cronograma rígido nem garante que todas as etapas serão executadas.

A continuidade do projeto dependerá de:

- resultados das etapas anteriores;
- disponibilidade da equipe;
- orientação docente;
- interesse dos estudantes;
- viabilidade técnica;
- possibilidades institucionais.

A ideia central é permitir que o ConectaCC cresça de forma progressiva, evitando desenvolver funcionalidades complexas antes de validar se elas realmente são úteis.

---

# Visão geral

A evolução do ConectaCC está organizada nas seguintes etapas:

1. ✅ Definição do produto e prototipação
2. ✅ Checkpoint inicial e ajustes de produto
3. ✅ Definição da arquitetura técnica
4. ✅ Implementação funcional do MVP
5. 🔄 Testes internos e validações finais
6. 🔄 Preparação do piloto
7. ⏳ Piloto com estudantes
8. ⏳ Revisão após o piloto
9. ⏳ Possível continuidade institucional / extensão
10. ⏳ Possível projeto de pesquisa
11. ⏳ Possível produção científica
12. ⏳ Evoluções futuras do produto

O roadmap não representa um cronograma rígido.

As etapas futuras dependem dos resultados obtidos nas fases anteriores, da disponibilidade da equipe, da orientação docente e da viabilidade técnica e institucional.

---

# Fase 1 — Definição do produto e prototipação

## Objetivo

Transformar a ideia do ConectaCC em um produto com escopo compreensível, requisitos definidos e representação visual antes da implementação.

## Atividades realizadas

- definição do problema;
- definição do público inicial;
- definição dos objetivos;
- delimitação do MVP;
- criação da identidade visual;
- definição da navegação;
- definição dos principais fluxos;
- definição dos papéis ALUNO, MODERADOR e ADMINISTRADOR;
- prototipação das principais telas;
- definição de estados de erro, loading e vazio;
- consideração de responsividade e acessibilidade;
- auditoria geral do protótipo;
- registro das decisões e pendências.

## Resultado

Foi produzido um protótipo navegável e documentado que serviu como referência para a implementação.

Durante a evolução do projeto, algumas decisões do protótipo foram ajustadas.

Entre elas:

- Oportunidade passou a fazer parte da versão funcional;
- Comunidade passou a funcionar como mural de Materiais e Oportunidades;
- busca de estudantes permaneceu para evolução futura;
- Publicação como tipo próprio de conteúdo permaneceu fora do MVP;
- os papéis MODERADOR e ADMINISTRADOR foram separados.

## Situação

✅ **Concluída.**

---

# Fase 2 — Definição da arquitetura técnica

## Objetivo

Definir como o MVP seria construído, preservando os requisitos de Produto já estabelecidos.

## Principais decisões realizadas

Foram definidos:

- stack;
- arquitetura;
- frontend;
- backend;
- banco de dados;
- autenticação;
- autorização;
- hospedagem;
- deploy;
- estratégia de testes;
- segurança;
- organização do código;
- estratégia de branches e integração contínua.

As decisões técnicas estão registradas principalmente em:

`stack-e-decisoes-tecnicas.md`

## Responsabilidade principal

Marcelo Henrique Germiniani Panho, com participação da equipe na validação das decisões relacionadas ao Produto.

## Princípio

A tecnologia deve atender ao Produto definido.

Necessidades técnicas que alterem comportamento, escopo, experiência do usuário ou regras de negócio devem ser discutidas com a equipe antes da mudança.

## Situação

✅ **Concluída.**

---

# Fase 3 — Implementação do MVP

## Objetivo

Transformar as definições de Produto e o protótipo em uma aplicação funcional.

## Principais entregas

Foram implementados:

1. fundação técnica;
2. autenticação;
3. layout global;
4. Perfil;
5. Home;
6. Estudos;
7. Materiais;
8. Meus conteúdos;
9. Perfil público;
10. Moderação;
11. Notificações;
12. Configurações;
13. painel administrativo `/admin`;
14. Auditoria;
15. Oportunidade;
16. Comunidade.

Também foram implementadas as regras de autorização para os papéis:

- ALUNO;
- MODERADOR;
- ADMINISTRADOR.

## Resultado

Existe uma versão funcional do MVP capaz de ser utilizada em testes internos e preparada para um piloto controlado.

Funcionalidades futuras registradas na documentação não são consideradas pendências para concluir esta fase.

## Situação

✅ **Implementação funcional do MVP concluída.**

As atividades seguintes concentram-se em qualidade, testes, revisão do ambiente e preparação do piloto.

---

# Fase 4 — Testes internos

## Objetivo

Identificar problemas antes de disponibilizar o sistema para os participantes do piloto.

## Áreas a validar

A rodada final deve contemplar:

- Cadastro;
- Login;
- controle de acesso por papel;
- Home;
- Perfil;
- seleção de disciplinas;
- Estudos;
- Materiais;
- Meus conteúdos;
- Perfil público;
- Oportunidade;
- Comunidade;
- Configurações;
- alteração de senha;
- Moderação;
- Notificações;
- painel `/admin`;
- Auditoria;
- links externos;
- estados de loading;
- estados de erro;
- estados vazios;
- buscas e filtros;
- concorrência;
- responsividade;
- acessibilidade.

A recuperação de senha não faz parte da versão atual e, portanto, não integra a rodada de validação do MVP.

## Qualidade automatizada

O projeto já possui uma base de testes automatizados para backend e frontend, incluindo:

- Pest;
- Vitest;
- Testing Library;
- testes de mutação;
- análise estática;
- Playwright;
- integração contínua.

A rodada final deve complementar essa cobertura com a validação funcional do MVP completo.

## Resultado esperado

Uma versão suficientemente estável para um piloto controlado.

## Situação

🔄 **Em andamento.**

---

# Fase 5 - Preparação do piloto

## Objetivo

Preparar o uso real do ConectaCC por um grupo controlado de estudantes.

## Configuração definida

O piloto foi planejado inicialmente para:

**12 participantes**

sendo:

- 10 estudantes com papel ALUNO;
- 2 ADMINISTRADORES: Marcelo e Viviane.

O cadastro ficará restrito aos participantes convidados por meio de:

`ALLOWED_EMAILS`

O domínio institucional utilizado é:

`academico.ifsul.edu.br`

A Matriz 2023, com as 49 disciplinas obrigatórias cadastradas, é a referência acadêmica do piloto.

Não haverá confirmação de propriedade do e-mail por link ou código nesta versão.

## Antes do piloto

Ainda devem ser concluídos:

- selecionar os 10 estudantes participantes;
- cadastrar os participantes em `ALLOWED_EMAILS`;
- revisar os Termos de participação;
- garantir que os Termos reflitam as regras atuais de Perfil, autoria, anonimato e tratamento dos dados;
- definir o canal operacional para solicitações de exclusão de conta;
- revisar o tratamento de dados considerando a LGPD e orientações institucionais;
- revisar os links institucionais utilizados;
- preparar o instrumento de feedback;
- definir o que será observado durante os testes;
- concluir a rodada final de testes internos;
- validar responsividade;
- validar acessibilidade;
- corrigir problemas bloqueadores;
- validar o ambiente de produção.

## Situação

🔄 **Em preparação.**
---

# Fase 6 - Piloto com estudantes

## Objetivo

Avaliar o ConectaCC em uso real por estudantes de Ciência da Computação do IFSul – Câmpus Passo Fundo.

## Participantes planejados

O piloto inicial foi planejado para:

- 10 estudantes participantes com papel ALUNO;
- 2 integrantes responsáveis pelo acompanhamento com papel ADMINISTRADOR.

A composição poderá ser ajustada caso exista necessidade durante a preparação da validação.

## Aspectos a observar

Entre os principais pontos:

- compreensão da proposta do ConectaCC;
- facilidade de uso;
- facilidade para encontrar materiais;
- utilidade de Estudos;
- interesse em compartilhar materiais;
- utilidade da área Oportunidade;
- interesse em indicar oportunidades;
- utilidade do mural da Comunidade;
- facilidade para encontrar recursos institucionais;
- compreensão da Moderação;
- utilidade de Meus conteúdos;
- utilidade das notificações;
- compreensão da autoria identificada e anônima dos materiais;
- dificuldades de navegação;
- problemas de responsividade ou acessibilidade;
- funcionalidades consideradas desnecessárias;
- necessidades não previstas.

## Feedback

O piloto deve utilizar um instrumento estruturado de feedback.

Podem ser utilizados:

- formulário;
- perguntas de usabilidade;
- espaço para sugestões;
- observação do uso;
- registro de dificuldades.

## Situação

⏳ **Aguardando a conclusão da preparação do piloto.**
---

# Fase 7 — Revisão após o piloto

## Objetivo

Transformar feedback em decisões de produto.

## Atividades

- organizar os feedbacks;
- identificar problemas recorrentes;
- separar bug de necessidade;
- identificar funcionalidades pouco utilizadas;
- identificar funcionalidades valorizadas;
- revisar prioridades;
- revisar personas;
- revisar requisitos;
- revisar fluxos;
- avaliar se a combinação entre autoria identificada e compartilhamento anônimo de materiais funcionou adequadamente;
- corrigir problemas.

## Resultado esperado

Uma nova versão do ConectaCC baseada em evidências de uso, e não apenas em hipóteses da equipe.

---

# Fase 8 — Possível continuidade como projeto de extensão

Esta etapa depende de orientação e enquadramento institucional.

O ConectaCC poderá futuramente ser avaliado como uma iniciativa de extensão ou continuidade institucional caso exista:

- interesse da comunidade acadêmica;
- utilidade demonstrada;
- condições de manutenção;
- orientação docente;
- aprovação institucional adequada.

## Possíveis objetivos

- ampliar participação de estudantes;
- envolver novos colaboradores;
- melhorar os recursos existentes;
- criar ações de integração;
- ampliar os recursos acadêmicos e, quando necessário, contemplar outras matrizes e disciplinas eletivas;
- aproximar estudantes de diferentes semestres;
- fortalecer circulação de oportunidades.

O projeto não deve afirmar antecipadamente que se tornará projeto de extensão.

Essa possibilidade deverá ser avaliada formalmente.

---

# Fase 9 — Possível projeto de pesquisa

Depois de possuir uma versão funcional e dados de uso, o ConectaCC poderá gerar perguntas de pesquisa.

Exemplos de questões possíveis:

- Quais informações acadêmicas os estudantes têm mais dificuldade de localizar?
- Uma plataforma de integração reduz a percepção de fragmentação dos recursos acadêmicos?
- Quais funcionalidades colaborativas geram maior participação?
- Estudantes compartilham materiais quando existe Moderação?
- Como estudantes percebem o compartilhamento anônimo ou identificado de materiais?
- O Perfil público e as contribuições identificadas ajudam os estudantes a perceber melhor quem faz parte da comunidade acadêmica?
- Quais fatores influenciam a adoção de uma plataforma acadêmica colaborativa?
- Quais barreiras impedem estudantes de utilizar recursos institucionais já existentes?
- O mural da Comunidade facilita a descoberta de materiais e oportunidades?

Essas questões são exemplos.

Uma pesquisa real deverá possuir:

- problema de pesquisa;
- metodologia;
- orientação;
- instrumentos adequados;
- cuidados éticos;
- análise dos dados.

O piloto de produto não deve ser automaticamente tratado como pesquisa científica.

---

# Fase 10 — Possível produção científica

Caso exista uma pesquisa formal, resultados do ConectaCC poderão futuramente contribuir para:

- artigo científico;
- resumo expandido;
- apresentação em evento;
- trabalho acadêmico;
- projeto científico;
- TCC;
- estudos sobre permanência, integração ou experiência estudantil.

A produção científica deverá ser baseada em:

- metodologia definida;
- dados adequados;
- análise;
- referências científicas;
- orientação acadêmica.

O desenvolvimento do software, sozinho, não deve ser apresentado automaticamente como pesquisa científica.

---

# Fase 11 — Possível expansão

Somente depois de validar o funcionamento no contexto inicial deverá ser avaliada expansão.

Possibilidades:

- disciplinas eletivas;
- Matriz 2017 ou outras matrizes curriculares;
- outros cursos;
- outros câmpus;
- outras instituições;
- novas iniciativas institucionais.

Caso o ConectaCC seja futuramente expandido para fora do IFSul, também poderá ser necessário avaliar:

- suporte a e-mails institucionais de outras instituições;
- novas regras de domínio;
- adequação dos recursos institucionais apresentados;
- diferenças de estrutura acadêmica entre instituições.

Essas questões não pertencem ao MVP nem ao piloto atual.
---

# Fase 12 — Funcionalidades futuras possíveis

As funcionalidades abaixo já foram discutidas, mas permanecem fora do MVP atual.

## Apoio e descoberta entre estudantes

- busca de estudantes;
- busca por disciplinas atuais;
- botão "Preciso de ajuda";
- identificação de colegas que podem ajudar;
- matching;
- pedidos de ajuda;
- acompanhamento de dúvidas.

## Comunicação e Comunidade

- chat;
- mensagens diretas;
- espaços de dúvidas;
- comentários;
- reações;
- curtidas;
- seguidores;
- canais da comunidade;
- publicação externa como tipo próprio de conteúdo.

## Estudos

- upload direto de arquivos;
- favoritos;
- avaliações;
- trilhas de estudo;
- organização mais avançada dos materiais.

## Perfil

- upload de foto;
- controles adicionais de visibilidade;
- ocultação de disciplinas;
- opção de não participar da descoberta social;
- inclusão do semestre, caso seja considerada útil.

## Oportunidades

- favoritar oportunidades;
- filtro "Somente salvas";
- alertas;
- filtros adicionais;
- edição de oportunidade aprovada;
- acompanhamento mais avançado de oportunidades.

### Favoritos

A regra principal já definida prevê:

- bookmark nos cards;
- favoritos individuais;
- filtro separado "Somente salvas";
- possibilidade de combinar favoritos com o filtro de tipo.

Os detalhes de UX e comportamento de oportunidades encerradas ou removidas serão definidos quando essa funcionalidade entrar no escopo.

## Moderação e Administração

- retirada administrativa de conteúdo aprovado;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão de conteúdo;
- histórico administrativo mais detalhado;
- contador de pendências na Moderação;
- auditoria de consultas;
- auditoria de Login;
- exportação de eventos de Auditoria.

A Auditoria das ações que alteram dados já faz parte do MVP atual.

## Autenticação e conta

- recuperação de senha;
- confirmação de propriedade do e-mail institucional por link ou código;
- aviso por e-mail após alteração de senha;
- autoexclusão de conta pela interface;
- suporte a outros domínios institucionais caso exista expansão do projeto;
- políticas adicionais de segurança conforme a evolução do sistema.

## Inteligência Artificial

Possibilidades futuras poderão ser estudadas, como:

- orientação;
- descoberta de recursos;
- apoio na navegação;
- recomendações;
- Agente Orienta.

Nenhuma dessas funcionalidades de Inteligência Artificial faz parte do MVP atual.

---

# Princípios para decidir evoluções

Uma nova funcionalidade não deve entrar apenas porque é tecnicamente interessante.

Antes de adicionar algo, perguntar:

1. Qual problema real isso resolve?
2. Estudantes demonstraram necessidade?
3. Já existe uma ferramenta institucional que resolve isso?
4. O ConectaCC deve construir ou apenas conectar essa solução?
5. A funcionalidade aumenta muito a complexidade?
6. É possível testar uma versão mais simples primeiro?
7. Existe capacidade de manter a funcionalidade depois?

---

# Princípio de evolução

O ConectaCC deve crescer de forma gradual:

> **definir → construir → testar → aprender → ajustar → evoluir**

O objetivo não é desenvolver o maior número possível de funcionalidades.

O objetivo é construir algo útil, sustentável e coerente com as necessidades reais da comunidade acadêmica.
