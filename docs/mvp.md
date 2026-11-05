# Escopo do MVP — ConectaCC

Este documento define o escopo atual do MVP do ConectaCC.

O objetivo é deixar claro o que faz parte da primeira versão do projeto, o que está sendo prototipado e o que deverá permanecer para evoluções futuras.

---

## 1. Objetivo do MVP

O MVP do ConectaCC busca validar a utilidade de uma plataforma colaborativa que conecte estudantes, conhecimento, oportunidades e recursos acadêmicos do curso de Ciência da Computação do IFSul – Câmpus Passo Fundo.

A proposta não é substituir sistemas e páginas que já existem.

O ConectaCC busca:

- facilitar a descoberta de recursos;
- organizar acessos relevantes;
- criar novas formas de colaboração entre estudantes;
- facilitar o compartilhamento de materiais;
- divulgar oportunidades;
- aproximar estudantes com interesses e necessidades semelhantes.

O MVP deve ser suficientemente simples para ser desenvolvido e testado, mas completo o bastante para permitir avaliar se a proposta realmente é útil para os estudantes.

---

## 2. Público inicial

O público inicial do MVP é composto por estudantes do curso de:

**Ciência da Computação — IFSul Câmpus Passo Fundo**

A expansão para outros cursos, câmpus ou instituições não faz parte do MVP atual.

---

## 3. Papéis de usuário

O MVP possui três papéis. As regras completas estão em [`requisitos.md`](requisitos.md), seção 1.

### ALUNO

Representa o estudante que utiliza normalmente o ConectaCC.

Pode:

- criar conta;
- realizar Login;
- recuperar senha, se a recuperação for implementada no MVP;
- acessar a Home;
- editar o próprio Perfil;
- selecionar disciplinas atuais;
- acessar Estudos;
- consultar materiais;
- compartilhar materiais por link;
- consultar Perfis públicos;

> **Atualização (02/11/2026):** consultar e indicar oportunidades, acessar a Comunidade, compartilhar publicações e encontrar outros estudantes saíram do MVP e ficam para a próxima versão (`decisoes.md` 56).
- consultar e gerenciar os próprios conteúdos em Perfil → Meus conteúdos;
- acessar Configurações;
- alterar senha;
- receber notificações.

### MODERADOR

Possui as funcionalidades disponíveis ao estudante e também pode acessar a **Moderação** dentro da aplicação comum (fora do `/admin`), para analisar, aprovar e rejeitar Material, Publicação e Oportunidade.

- É nomeado somente por ADMINISTRADOR.
- Não acessa `/admin`, não altera papéis e não gerencia permissões.
- Não modera o próprio conteúdo: esse conteúdo não aparece na fila dele.
- Nunca vê a identidade real do autor de conteúdo anônimo.

### ADMINISTRADOR

Possui as funcionalidades disponíveis ao estudante, pode moderar (inclusive o próprio conteúdo) e é o único com acesso ao painel administrativo em `/admin`, onde gerencia papéis e permissões e pode identificar o autor real de conteúdo anônimo quando necessário.

---

# 4. Áreas do MVP

> **Atualização (04/11/2026):** Oportunidade e Comunidade saíram do MVP (`decisoes.md` 56).

## 4.1 Autenticação

O MVP contempla:

- Login;
- Cadastro;
- Termos de participação;
- Recuperação de senha;
- Logout.

### Cadastro

O cadastro utiliza:

- Nome completo;
- E-mail institucional;
- Senha;
- Confirmar senha.

O Nome completo é utilizado inicialmente como Nome de exibição e poderá ser alterado posteriormente no Perfil.

No MVP e no piloto, o cadastro utiliza exclusivamente:

**e-mail institucional do IFSul.**

A possibilidade de aceitar e-mails de outras instituições poderá ser avaliada apenas em uma futura expansão do ConectaCC para fora do IFSul.

No MVP e no piloto não haverá confirmação de propriedade do e-mail por link ou código.

Essa funcionalidade poderá ser avaliada em uma evolução futura e não interfere no fluxo de recuperação de senha.

### Senha

Regra inicial:

- mínimo de 8 caracteres;
- confirmação deve ser igual à senha.

Não existem, no MVP atual, exigências adicionais obrigatórias de letras maiúsculas, números ou símbolos.

### Termos de participação

Antes da criação da conta, o estudante deve aceitar os Termos de participação da versão de testes.

O aceite deverá ser registrado de forma versionada durante a implementação.

---

# 5. Home

> **Atualização (04/11/2026):** os atalhos são só para Estudos e Perfil (`decisoes.md` 56).

A Home funciona como ponto inicial do ConectaCC.

Ela apresenta:

- saudação;
- breve apresentação da plataforma;
- atalhos para as principais áreas;
- notificações;
- orientação para completar o Perfil quando necessário.

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

Enquanto isso não ocorrer, a Home apresenta:

**Complete seu perfil**

Depois que o Perfil estiver completo, esse card deixa de ser exibido.

---

# 6. Perfil

O Perfil permite ao estudante apresentar informações acadêmicas e interesses dentro da comunidade.

## Obrigatórios para Perfil completo

- Nome de exibição;
- pelo menos uma disciplina atual.

## Opcionais

- Sobre mim;
- Interesses;
- Posso ajudar com;
- Procuro ajuda com;
- GitHub;
- LinkedIn.

O curso no MVP é:

**Ciência da Computação**

O e-mail institucional não aparece no Perfil público.

O avatar utiliza as iniciais do Nome de exibição.

Upload de foto fica para evolução futura.

---

# 7. Estudos

Estudos é a área de apoio acadêmico do ConectaCC.

Ela possui:

## Minhas disciplinas

Apresenta as disciplinas selecionadas pelo estudante no Perfil.

## Todas as disciplinas

Apresenta o catálogo das disciplinas confirmadas do curso.

A referência acadêmica inicial do MVP será a:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Fontes institucionais identificadas:

Grade Curricular:

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

Matriz / Organograma de pré-requisitos:

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

A necessidade de contemplar estudantes ainda vinculados à Matriz 2017 será verificada posteriormente e não bloqueia as atividades atuais.

> **Atualização (04/11/2026):** substituído. Todas as disciplinas têm área de materiais (`decisoes.md` 54.2).

## Página da disciplina

Uma disciplina pode apresentar:

- informações acadêmicas;
- acesso à fonte oficial da ementa, quando confirmada;
- materiais aprovados pela Moderação;
- opção para compartilhar material.

---

# 8. Compartilhamento de materiais

No MVP, materiais são compartilhados através de:

**links**

Não existe upload direto de arquivos nesta versão.

Fluxo:

Aluno acessa disciplina
→ Compartilhar material
→ informa Título, Descrição e Link
→ Enviar para análise
→ conteúdo fica PENDENTE
→ ADMIN analisa.

Se aprovado:

→ material aparece em Estudos.

Se rejeitado:

→ material não é publicado.

> **Atualização (04/11/2026):** substituído. O card mostra o nome de exibição do autor, com link para o Perfil público. Com a opção "Compartilhar anonimamente", o card mostra "Compartilhado pela comunidade", sem link, e o sistema continua guardando quem enviou (`decisoes.md` 55.2 e 57.2).

O ADMIN continua identificando quem realizou o envio para fins de Moderação e notificação.

Quando um material for aprovado, sua data pública será:

**a data de aprovação/publicação.**

A data original do envio poderá permanecer registrada internamente.
---

# 9. Oportunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

A área Oportunidade possui duas origens diferentes.

## Oportunidades do IFSul

Reúne acessos institucionais já existentes.

Exemplos atualmente previstos:

- Estágios;
- Projetos de Extensão.

As URLs devem ser confirmadas antes de serem utilizadas como links reais.

## Oportunidades indicadas pela comunidade

Estudantes podem indicar oportunidades externas.

Campos:

- Título;
- Tipo;
- Descrição breve;
- Link.

Tipos iniciais:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

As oportunidades indicadas passam por Moderação antes de aparecer para outros estudantes.

A data apresentada publicamente corresponde à:

**data de aprovação/publicação.**

A data original do envio permanece como informação interna.

---

# 10. Comunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

A Comunidade busca aproximar estudantes e facilitar a circulação de conhecimentos e informações.

Ela possui três áreas principais:

## O que está rolando

Feed cronológico simples.

Pode apresentar:

- novos materiais;
- publicações compartilhadas por estudantes.

O feed utiliza ordem do mais recente para o mais antigo.

Para publicações que passam por Moderação, a referência pública de data utiliza:

**a data de aprovação/publicação.**

Não existe algoritmo de recomendação no MVP.

## Encontre estudantes

Permite pesquisar outros estudantes por:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

O próprio usuário não aparece nos próprios resultados.

Sem busca ativa, a lista utiliza ordem alfabética.

## Conecte-se com a comunidade

Área destinada a acessos relevantes.

Recursos já discutidos:

- grupo de WhatsApp dos estudantes;
- Instagram institucional;
- Arena Games.

Links ainda não confirmados não devem ser inventados.

---

# 11. Perfil público

> **Atualização (04/11/2026):** implementado sem o Semestre e sem lista de materiais. O acesso é pelo nome do autor no material, numa URL com código aleatório (`decisoes.md` 57).

O Perfil público pode ser visualizado apenas por usuários autenticados no ConectaCC.

Pode apresentar:

- avatar;
- Nome de exibição;
- Curso;
- Sobre;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais;
- GitHub;
- LinkedIn.

Não apresenta:

- e-mail institucional;
- Nome completo da conta quando diferente do Nome de exibição;
- dados de autenticação;
- informações administrativas.

As disciplinas atuais são visíveis para outros estudantes autenticados.

Essa informação deverá aparecer explicitamente nos Termos antes do piloto real.

---

# 12. Configurações

No MVP, Configurações possui somente funções necessárias.

## Conta

- E-mail institucional em modo somente leitura.

## Segurança

Permite alterar a senha utilizando:

- Senha atual;
- Nova senha;
- Confirmar nova senha.

Não existem no MVP atual:

- tema;
- idioma;
- preferências de feed;
- configurações sociais;
- exclusão automática de conta;
- preferências avançadas de notificações.

---

# 13. Notificações

As notificações estão principalmente relacionadas aos resultados da Moderação.

Exemplos:

- material aprovado;
- material rejeitado;
- publicação aprovada;
- publicação rejeitada;
- oportunidade aprovada;
- oportunidade rejeitada.

> **Atualização (02/11/2026):** no MVP, só materiais e alterações de material geram notificação. Publicação e Oportunidade saíram do MVP (`decisoes.md` 56).

Quando houver motivo de rejeição, ele poderá aparecer na notificação.

O MVP utiliza:

- sino de notificações;
- contador de não lidas;
- dropdown;
- marcar como lida;
- marcar todas como lidas.

Não é necessária uma página específica de notificações nesta primeira versão.

O comportamento ao clicar em uma notificação específica será definido antes da implementação definitiva de Notificações.

Essa decisão não bloqueia a definição da arquitetura geral.

---

# 14. Moderação

A Moderação é realizada por MODERADOR e ADMINISTRADOR. ALUNO não acessa a Moderação.

Os conteúdos moderados são:

- MATERIAL;
- PUBLICAÇÃO;
- OPORTUNIDADE.

> **Atualização (02/11/2026):** no MVP, a fila tem só MATERIAL (`decisoes.md` 56).

Estados:

- PENDENTE;
- APROVADO;
- REJEITADO.

Fluxo:

Conteúdo enviado
→ PENDENTE
→ MODERADOR ou ADMINISTRADOR analisa
→ APROVADO ou REJEITADO
→ autor recebe notificação.

## Aprovação

Quando aprovado:

- Material → aparece em Estudos;
- Publicação → aparece na Comunidade;
- Oportunidade → aparece em Oportunidade.

## Rejeição

Quando rejeitado:

- não aparece publicamente;
- autor recebe notificação;
- motivo da rejeição é obrigatório (`decisoes.md` 55.4).

## Conflito de interesse

Um MODERADOR não pode aprovar ou rejeitar o próprio conteúdo, inclusive o anônimo. Esse conteúdo não aparece na fila dele e é analisado por outro MODERADOR ou por um ADMINISTRADOR.

O ADMINISTRADOR pode moderar o próprio conteúdo.

---

# 15. Navegação principal

## ALUNO

- Home
- Estudos
- Perfil

(Oportunidade e Comunidade saíram do MVP — `decisoes.md` 56.)

Grupo inferior:

- Configurações
- Sair

## MODERADOR e ADMINISTRADOR

Possuem os mesmos itens e adicionalmente acesso à Moderação, dentro da aplicação comum.

O painel administrativo em `/admin` é exclusivo do ADMINISTRADOR.

A navegação exata de MODERADOR e ADMINISTRADOR será definida na rodada específica desses papéis.

---

# 16. O que NÃO pertence ao MVP

> **Atualização (04/11/2026):** além da lista abaixo, Oportunidade, Comunidade e busca de estudantes ficam para a próxima versão (`decisoes.md` 56). Se houver tempo, a Comunidade pode voltar ainda durante a etapa de testes (57.2).

Não implementar nesta primeira versão:

- chat;
- mensagens diretas;
- comentários;
- curtidas;
- reações;
- seguidores;
- sistema de amizade;
- matching automático;
- botão Pedir ajuda;
- inteligência artificial;
- recomendações personalizadas;
- ranking;
- gamificação;
- upload direto de materiais;
- upload de foto de Perfil;
- confirmação de propriedade do e-mail institucional por link ou código;
- candidatura a oportunidades dentro do sistema;
- feed algorítmico;
- rede social completa;
- CRUD administrativo completo;
- histórico administrativo completo;
- remoção de conteúdo já aprovado;
- expansão para outros cursos.

Essas funcionalidades poderão ser avaliadas posteriormente, principalmente a partir dos resultados do piloto.

---

# 17. Objetivo da validação

O MVP deverá permitir avaliar questões como:

- os estudantes entendem a proposta do ConectaCC?
- Estudos facilita o acesso a materiais?
- estudantes têm interesse em compartilhar conteúdos?
- ~~a área Oportunidade é útil?~~ (fora do MVP)
- ~~a Comunidade facilita a descoberta de colegas e interesses?~~ (fora do MVP)
- os recursos institucionais ficam mais fáceis de encontrar?
- o processo de Moderação é compreensível?
- quais funcionalidades realmente geram valor?
- quais partes devem ser alteradas, removidas ou ampliadas?

O resultado dessa validação deverá orientar as próximas versões do projeto.
