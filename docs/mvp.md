# Escopo do MVP - ConectaCC

Este documento define o escopo atual do MVP do ConectaCC.

O objetivo é deixar claro o que faz parte da versão atual do projeto e o que permanece previsto para evoluções futuras.

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
- acessar a Home;
- editar o próprio Perfil;
- selecionar disciplinas atuais;
- acessar Estudos;
- consultar materiais;
- compartilhar materiais por link;
- consultar Perfis públicos;
- acessar Oportunidade;
- consultar e indicar oportunidades;
- acessar a Comunidade;
- consultar e gerenciar os próprios conteúdos em Perfil → Meus conteúdos;
- acessar Configurações;
- alterar senha;
- receber notificações.

A busca de estudantes e outras funcionalidades sociais permanecem para evoluções futuras.

### MODERADOR

Possui as funcionalidades disponíveis ao estudante e também pode acessar a **Moderação** dentro da aplicação comum.

Pode analisar:

- Materiais;
- Oportunidades.

Regras:

- é nomeado somente por ADMINISTRADOR;
- não acessa `/admin`;
- não altera papéis;
- não gerencia permissões administrativas;
- não pode moderar o próprio conteúdo;
- o próprio conteúdo não aparece na fila que ele pode moderar;
- não visualiza a identidade real do autor de um material enviado anonimamente.

### ADMINISTRADOR

Possui as funcionalidades disponíveis ao estudante e também:

- acessa a Moderação;
- pode moderar o próprio conteúdo;
- acessa exclusivamente o painel administrativo em `/admin`;
- pode alterar papéis de outras contas;
- consulta informações administrativas;
- acessa a Auditoria;
- pode identificar o autor real de material anônimo nas áreas administrativas autorizadas.

---

# 4. Áreas do MVP

## 4.1 Autenticação

O MVP contempla:

- Login;
- Cadastro;
- Termos de participação;
- Logout;
- alteração de senha em Configurações.

A recuperação de senha não faz parte da versão atual.

### Cadastro

O cadastro utiliza:

- Nome completo;
- E-mail institucional;
- Senha;
- Confirmar senha.

O Nome completo é utilizado inicialmente como Nome de exibição e poderá ser alterado posteriormente no Perfil.

No MVP e no piloto, o cadastro utiliza exclusivamente o domínio institucional definido para o projeto:

**`academico.ifsul.edu.br`**

Durante o período de teste fechado, o cadastro também pode ser limitado aos e-mails convidados.

A possibilidade de aceitar e-mails de outras instituições poderá ser avaliada apenas em uma futura expansão do ConectaCC.

### Confirmação de propriedade do e-mail

No MVP e no piloto não haverá confirmação de cadastro por link ou código enviado ao e-mail.

Essa funcionalidade poderá ser avaliada futuramente.

### Senha

Regra atual:

- mínimo de 8 caracteres;
- confirmação deve ser igual à senha.

Não existem, no MVP atual, exigências adicionais obrigatórias de letras maiúsculas, números ou símbolos.

### Termos de participação

Antes da criação da conta, o estudante deve aceitar os Termos de participação vigentes.

Os Termos são versionados.

O sistema registra:

- versão aceita;
- usuário;
- data e hora do aceite.

---

# 5. Home

## 4.1 Autenticação

O MVP contempla:

- Login;
- Cadastro;
- Termos de participação;
- Logout;
- alteração de senha em Configurações.

A recuperação de senha não faz parte da versão atual.

### Cadastro

O cadastro utiliza:

- Nome completo;
- E-mail institucional;
- Senha;
- Confirmar senha.

O Nome completo é utilizado inicialmente como Nome de exibição e poderá ser alterado posteriormente no Perfil.

No MVP e no piloto, o cadastro utiliza exclusivamente o domínio institucional definido para o projeto:

**`academico.ifsul.edu.br`**

Durante o período de teste fechado, o cadastro também pode ser limitado aos e-mails convidados.

A possibilidade de aceitar e-mails de outras instituições poderá ser avaliada apenas em uma futura expansão do ConectaCC.

### Confirmação de propriedade do e-mail

No MVP e no piloto não haverá confirmação de cadastro por link ou código enviado ao e-mail.

Essa funcionalidade poderá ser avaliada futuramente.

### Senha

Regra atual:

- mínimo de 8 caracteres;
- confirmação deve ser igual à senha.

Não existem, no MVP atual, exigências adicionais obrigatórias de letras maiúsculas, números ou símbolos.

### Termos de participação

Antes da criação da conta, o estudante deve aceitar os Termos de participação vigentes.

Os Termos são versionados.

O sistema registra:

- versão aceita;
- usuário;
- data e hora do aceite.
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

A referência acadêmica utilizada no MVP é a:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Fontes institucionais identificadas:

Grade Curricular:

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

Matriz / Organograma de pré-requisitos:

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

A necessidade de contemplar estudantes ainda vinculados à Matriz 2017 será verificada posteriormente e não bloqueia as atividades atuais.

Todas as disciplinas confirmadas da Matriz 2023 possuem área para compartilhamento e consulta de materiais.

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

## Fluxo

Aluno acessa disciplina  
→ Compartilhar material  
→ informa Título, Descrição opcional e Link  
→ escolhe se deseja compartilhar anonimamente  
→ Enviar para análise  
→ conteúdo fica PENDENTE  
→ MODERADOR ou ADMINISTRADOR analisa.

## Se aprovado

→ material aparece em Estudos  
→ também pode aparecer no mural da Comunidade  
→ autor recebe notificação.

## Se rejeitado

→ material não é publicado  
→ autor recebe notificação  
→ motivo fica disponível em Meus conteúdos.

## Autoria

Por padrão, o material apresenta publicamente o **Nome de exibição** do autor.

O estudante também pode escolher:

**Compartilhar anonimamente**

Nesse caso, o material aparece publicamente como:

**Compartilhado pela comunidade**

O sistema continua registrando internamente quem realizou o envio.

O MODERADOR não visualiza a identidade real do autor de um material anônimo.

O ADMINISTRADOR pode identificar o autor nas áreas administrativas autorizadas.

## Data pública

Quando aprovado, o material utiliza publicamente:

**a data de aprovação/publicação.**

A data original do envio permanece registrada internamente.
---

# 9. Oportunidade

Oportunidade faz parte do MVP atual.

A área reúne:

- acesso a oportunidades institucionais;
- oportunidades indicadas pela comunidade.

## Oportunidades institucionais

A página disponibiliza acesso a recursos oficiais já existentes, como as oportunidades e bolsas divulgadas pelo IFSul.

Links institucionais somente devem ser utilizados quando o endereço oficial estiver confirmado.

## Oportunidades indicadas pela comunidade

Estudantes podem indicar oportunidades externas.

Campos:

- Tipo;
- Título;
- Descrição curta;
- Link;
- Modalidade;
- Inscrições até, opcional.

Tipos:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

Modalidades:

- Remoto;
- Híbrido;
- Presencial.

As oportunidades indicadas passam por Moderação antes de aparecer para outros estudantes.

Quando aprovadas:

- aparecem na área Oportunidade;
- podem aparecer no mural da Comunidade;
- o autor recebe notificação.

A autoria de quem indicou a oportunidade não é apresentada publicamente.

## Prazo

O campo **Inscrições até** é opcional.

Quando não for informado, a oportunidade permanece visível por 15 dias após a aprovação.

Não fazem parte da versão atual:

- edição de oportunidade aprovada;
- envio anônimo.
---

# 10. Comunidade

A Comunidade faz parte do MVP atual e funciona como um mural de descoberta dos conteúdos compartilhados no ConectaCC.

O mural reúne:

- materiais aprovados;
- oportunidades aprovadas.

O usuário pode:

- buscar conteúdos;
- filtrar por tipo;
- alterar a ordem;
- alternar entre visualização em lista e grade;
- carregar mais resultados;
- acessar materiais;
- acessar oportunidades;
- utilizar a opção "Compartilhar conteúdo".

## Materiais

Materiais identificados podem apresentar o Nome de exibição do autor e permitir acesso ao Perfil público.

Materiais anônimos aparecem como compartilhados pela Comunidade e não revelam publicamente o autor.

## Oportunidades

Oportunidades aparecem como conteúdos indicados pela Comunidade.

A identidade de quem realizou a indicação não é apresentada publicamente.

## Evoluções futuras

Não fazem parte da versão atual:

- publicações como tipo próprio de conteúdo;
- comentários;
- canais da comunidade;
- busca de estudantes;
- funcionalidades de rede social.

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

Os Termos de participação informam que as informações preenchidas no Perfil podem ser visualizadas por outros usuários autenticados, enquanto o e-mail institucional permanece privado.
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
- autoexclusão de conta pela interface;
- preferências avançadas de notificações.

---

# 13. Notificações

As notificações estão relacionadas principalmente aos resultados da Moderação.

Eventos atuais:

- material aprovado;
- material rejeitado;
- alteração de material aprovada;
- alteração de material rejeitada;
- oportunidade aprovada;
- oportunidade rejeitada.

O MVP utiliza:

- sino de notificações;
- contador de não lidas;
- limite visual `9+`;
- dropdown;
- indicação de notificação não lida;
- marcar como lida;
- marcar todas como lidas;
- estado vazio.

Ao clicar em uma notificação:

→ ela é marcada como lida  
→ o usuário é direcionado ao conteúdo ou área correspondente.

Exemplos:

Material aprovado  
→ disciplina correspondente em Estudos.

Material rejeitado  
→ item correspondente em Meus conteúdos.

Oportunidade aprovada  
→ oportunidade correspondente.

Oportunidade rejeitada  
→ item correspondente em Meus conteúdos.

O motivo da rejeição fica disponível nos detalhes do envio em Meus conteúdos.

Não existe página específica de notificações no MVP.

---

# 14. Moderação

A Moderação é realizada por:

- MODERADOR;
- ADMINISTRADOR.

ALUNO não possui acesso à área.

A fila atual reúne:

- MATERIAL;
- OPORTUNIDADE.

A fila é única e apresenta conteúdos PENDENTES, dos mais antigos para os mais recentes.

Não existe filtro por tipo de conteúdo na versão atual.

## Estados

- PENDENTE;
- APROVADO;
- REJEITADO.

## Fluxo

Conteúdo enviado  
→ PENDENTE  
→ MODERADOR ou ADMINISTRADOR analisa  
→ APROVADO ou REJEITADO  
→ autor recebe notificação.

## Aprovação

Quando aprovado:

- Material → aparece em Estudos e na Comunidade;
- Oportunidade → aparece em Oportunidade e na Comunidade.

## Rejeição

Quando rejeitado:

- não aparece publicamente;
- autor recebe notificação;
- motivo da rejeição é obrigatório;
- motivo fica disponível ao autor em Meus conteúdos.

## Conflito de interesse

O MODERADOR não pode aprovar ou rejeitar o próprio conteúdo.

O próprio envio não aparece entre as pendências que ele pode moderar.

Essa regra também vale para materiais enviados anonimamente.

O ADMINISTRADOR pode moderar o próprio conteúdo.

## Anonimato

O MODERADOR não visualiza a identidade real do autor de material anônimo.

O ADMINISTRADOR pode consultar essa informação nas áreas administrativas autorizadas.

---

# 15. Navegação principal

## ALUNO

Menu principal:

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil

Grupo inferior:

- Configurações
- Sair

## MODERADOR

Possui os mesmos itens do ALUNO e adicionalmente:

- Moderação

O MODERADOR não possui acesso ao painel `/admin`.

## ADMINISTRADOR

Possui os mesmos itens do ALUNO e adicionalmente:

- Moderação
- Painel administrativo

O painel administrativo em `/admin` é exclusivo do ADMINISTRADOR.
---

# 16. O que NÃO pertence ao MVP

Não fazem parte da versão atual:

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
- busca de estudantes;
- canais da comunidade;
- publicação externa como tipo próprio de conteúdo;
- upload direto de materiais;
- upload de foto de Perfil;
- recuperação de senha;
- confirmação de propriedade do e-mail institucional por link ou código;
- candidatura a oportunidades dentro do sistema;
- edição de oportunidade aprovada;
- envio anônimo de oportunidade;
- feed algorítmico;
- rede social completa;
- CRUD administrativo completo;
- retirada administrativa de conteúdo aprovado pela interface;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão de conteúdo;
- autoexclusão de conta pela interface;
- expansão para outros cursos, câmpus ou instituições.

Essas funcionalidades poderão ser avaliadas posteriormente, principalmente a partir dos resultados do piloto e da evolução do projeto.

---

# 17. Objetivo da validação

O MVP deverá permitir avaliar questões como:

- os estudantes entendem a proposta do ConectaCC?
- Estudos facilita o acesso a materiais?
- estudantes têm interesse em compartilhar conteúdos?
- a área Oportunidade facilita a descoberta de oportunidades relevantes?
- estudantes têm interesse em indicar oportunidades para a comunidade?
- o mural da Comunidade facilita a descoberta de materiais e oportunidades?
- os recursos institucionais ficam mais fáceis de encontrar?
- o processo de Moderação é compreensível?
- as notificações ajudam o estudante a acompanhar os próprios envios?
- quais funcionalidades realmente geram valor?
- quais partes devem ser alteradas, removidas ou ampliadas?

O resultado dessa validação deverá orientar as próximas versões do projeto.
