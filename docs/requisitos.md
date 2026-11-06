# Requisitos do MVP — ConectaCC

Este documento registra os principais requisitos funcionais e regras de negócio do MVP do ConectaCC.

O objetivo é servir como referência para:

- implementação;
- testes;
- revisão funcional;
- validação do produto.

Os requisitos descritos aqui representam decisões de produto e não definem, por si só, a tecnologia que deverá ser utilizada.


> **Escopo atual (02/11/2026):** Oportunidade, Comunidade (feed, publicações e canais) e a busca de estudantes saíram do MVP e ficam para a próxima versão (`decisoes.md` 56). Os requisitos e fluxos dessas áreas continuam neste documento como referência.

---

# 1. Papéis de usuário

O MVP possui três papéis:

- ALUNO
- MODERADOR
- ADMINISTRADOR

Representação conceitual do nível de acesso:

ADMINISTRADOR
↓
MODERADOR
↓
ALUNO

Isso **não significa herança técnica automática**. A implementação usa permissões específicas (Policies e Gates).

## 1.1 ALUNO

O usuário ALUNO pode:

- criar conta;
- realizar Login;
- recuperar senha, se a recuperação for implementada no MVP;
- acessar Home;
- editar o próprio Perfil;
- selecionar disciplinas atuais;
- acessar Estudos;
- acessar Oportunidade;
- acessar Comunidade;
- consultar Perfis públicos;
- acessar Configurações;
- alterar a própria senha;
- receber notificações;
- enviar conteúdos para análise, identificados ou anônimos;
- consultar e gerenciar os próprios conteúdos em Perfil → Meus conteúdos;
- editar e excluir conteúdos de sua própria autoria, conforme as regras definidas.

O usuário ALUNO não pode:

- acessar a área de Moderação;
- consultar conteúdos pendentes de outros usuários;
- aprovar conteúdos;
- rejeitar conteúdos;
- visualizar informações administrativas;
- alterar papéis;
- gerenciar permissões.

## 1.2 MODERADOR

O MODERADOR é um usuário com permissões adicionais para auxiliar na Moderação. Possui as funcionalidades comuns do usuário e também pode:

- acessar as funções de Moderação que forem permitidas;
- analisar Material, Publicação e Oportunidade (no MVP, só Material — `decisoes.md` 56);
- aprovar solicitações permitidas;
- rejeitar solicitações permitidas, informando o motivo (obrigatório);
- solicitar revisão/remoção de conteúdo já publicado — **Pós-MVP / evolução futura** (`decisoes.md` 63.4).

Regras:

- somente ADMINISTRADOR pode nomear MODERADOR;
- a Moderação do MODERADOR fica dentro da aplicação comum, **fora do `/admin`**;
- o MODERADOR **não acessa `/admin`**;
- o MODERADOR não pode criar ou remover MODERADORES, criar ADMINISTRADORES, alterar papéis nem gerenciar permissões;
- o MODERADOR não acessa informações administrativas que não sejam necessárias.

### Conflito de interesse

O MODERADOR **não pode moderar o próprio conteúdo**, inclusive quando enviado anonimamente.

O conteúdo do próprio MODERADOR **não aparece na fila dele**; ele é analisado por outro MODERADOR ou por um ADMINISTRADOR.

O MODERADOR **nunca vê a identidade real** do autor de conteúdo anônimo. Ele vê a autoria como “Comunidade”.

## 1.3 ADMINISTRADOR

O ADMINISTRADOR possui as funcionalidades comuns do usuário e também pode:

- acessar `/admin`, que é exclusivo de ADMINISTRADORES;
- moderar, inclusive o próprio conteúdo;
- revisar decisões de Moderação — **Pós-MVP / evolução futura** (`decisoes.md` 63.4);
- reverter rejeições — **Pós-MVP / evolução futura** (`decisoes.md` 63.4);
- retirar conteúdo previamente aprovado/publicado — **Pós-MVP / evolução futura** (`decisoes.md` 63);
- nomear MODERADORES e remover o papel de MODERADOR;
- gerenciar papéis e permissões;
- consultar as informações administrativas necessárias;
- identificar o autor real de conteúdo anônimo **somente nas áreas administrativas autorizadas**.

Nas áreas públicas, o ADMINISTRADOR vê a autoria de conteúdo anônimo como “Comunidade”, igual aos demais usuários. Isso não significa acesso irrestrito a todos os dados.

A UX completa do painel administrativo e o fluxo detalhado das ações de MODERADOR e ADMINISTRADOR (revisão, reversão, retirada, auditoria) serão definidos em rodada específica.

> **Atualização (04/11/2026):** a retirada administrativa de conteúdo aprovado, a revisão de decisões de Moderação e a reversão de rejeições não são necessárias para concluir o `/admin` do MVP (`decisoes.md` 63).

---

# 2. Cadastro

## RF-01 — Criar conta

O sistema deve permitir o cadastro de um novo usuário.

Campos:

- Nome completo;
- E-mail institucional;
- Senha;
- Confirmar senha.

## RF-02 — Nome de exibição inicial

Ao criar a conta, o Nome completo deve ser utilizado inicialmente como Nome de exibição.

O usuário poderá alterar o Nome de exibição posteriormente no Perfil.

## RF-03 — Validação de senha

A senha deve possuir no mínimo:

**8 caracteres**

A confirmação deve ser igual à senha.

Não existem no MVP regras obrigatórias adicionais de:

- letra maiúscula;
- número;
- símbolo.

## RF-04 — Termos de participação

O cadastro só pode ser concluído depois que o usuário aceitar os Termos de participação.

O aceite deve possuir:

- versão;
- data/hora;
- usuário relacionado.

O aceite só deve ser registrado quando a conta for efetivamente criada.

## RF-05 — E-mail institucional

No MVP e no piloto, o cadastro deve utilizar:

**e-mail institucional do IFSul.**

A implementação deverá validar:

- formato válido de e-mail;
- domínio institucional aceito pelo IFSul.

A confirmação técnica do domínio ou dos domínios exatos utilizados pelo IFSul poderá ser realizada durante a implementação.

A possibilidade de aceitar e-mails institucionais de outras instituições fica fora do MVP atual e poderá ser avaliada somente em uma eventual expansão futura do ConectaCC.

## RF-05.1 — Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

O sistema não precisa exigir que o usuário confirme a propriedade do endereço antes de utilizar a conta.

Essa funcionalidade poderá ser avaliada em uma evolução futura do ConectaCC.

Essa regra não altera a recuperação de senha, que continuará utilizando envio de e-mail.
---

# 3. Login

## RF-06 — Autenticação

O usuário deve conseguir entrar utilizando:

- E-mail institucional;
- Senha.

## RF-07 — Credenciais inválidas

Em caso de falha de autenticação, utilizar mensagem genérica:

**E-mail ou senha incorretos.**

O sistema não deve revelar desnecessariamente qual credencial estava incorreta.

## RF-08 — Usuário autenticado

Usuário autenticado que acessar a tela de Login poderá ser redirecionado para a Home.

---

# 4. Recuperação de senha

## RF-09 — Solicitar recuperação

O usuário deve poder solicitar recuperação utilizando o E-mail institucional.

## RF-10 — Resposta genérica

Depois da solicitação, o sistema não deve confirmar se existe uma conta associada ao e-mail.

Exemplo:

**Se existir uma conta associada ao e-mail informado, enviaremos as instruções para redefinir sua senha.**

## RF-11 — Link de recuperação

A definição de nova senha deve ocorrer através de link recebido por e-mail.

A tela de nova senha não faz parte da navegação comum da aplicação.

## RF-12 — Nova senha

A nova senha deve possuir:

- mínimo de 8 caracteres;
- confirmação igual.

A política técnica do link de recuperação ainda deverá ser definida.

---

# 5. Home

## RF-13 — Home autenticada

Depois do Login, o usuário deve acessar a Home.

## RF-14 — Saudação

A Home deve apresentar saudação utilizando o Nome de exibição.

## RF-15 — Atalhos

A Home deve oferecer acesso às principais áreas:

- Estudos;
- Perfil.

(Oportunidade e Comunidade saíram do MVP — `decisoes.md` 56.)

## RF-16 — Perfil incompleto

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

Enquanto estiver incompleto, a Home deve apresentar:

**Complete seu perfil**

## RF-17 — Perfil completo

Depois que o Perfil estiver completo:

- o card “Complete seu perfil” deve desaparecer;
- não deve ser substituído por gamificação ou percentual de completude.

---

# 6. Perfil

## RF-18 — Visualizar Perfil

O usuário deve conseguir consultar o próprio Perfil.

## RF-19 — Editar Perfil

O usuário deve conseguir editar:

- Nome de exibição;
- Disciplinas atuais;
- Sobre mim;
- Interesses;
- Posso ajudar com;
- Procuro ajuda com;
- GitHub;
- LinkedIn.

## RF-20 — Campos obrigatórios

Para considerar o Perfil completo:

- Nome de exibição é obrigatório;
- pelo menos uma disciplina atual é obrigatória.

Os demais campos são opcionais.

## RF-21 — Curso

No MVP, o curso é:

**Ciência da Computação**

Não existe seleção de curso no Perfil.

## RF-22 — Avatar

O avatar deve utilizar iniciais do Nome de exibição.

No MVP não existe upload de foto.

## RF-23 — GitHub

Quando preenchido:

- deve utilizar endereço correspondente ao GitHub;
- deve abrir em nova guia na visualização pública.

## RF-24 — LinkedIn

Quando preenchido:

- deve utilizar endereço correspondente ao LinkedIn;
- deve abrir em nova guia na visualização pública.

## RF-25 — Alterações não salvas

Ao tentar sair com alterações ainda não salvas, o usuário deve receber confirmação antes de descartá-las.

---

# 7. Disciplinas atuais

## RF-26 — Seleção

O usuário deve conseguir selecionar as disciplinas que cursa atualmente.

## RF-27 — Integração com Estudos

As disciplinas selecionadas devem alimentar:

**Minhas disciplinas**

na área Estudos.

## RF-28 — Visibilidade

As disciplinas atuais podem ser vistas por outros estudantes autenticados no Perfil público.

Essa regra deverá estar explicitamente indicada nos Termos antes do piloto real.

---

# 8. Estudos

## RF-29 — Acessar Estudos

O usuário autenticado deve conseguir acessar Estudos.

## RF-30 — Minhas disciplinas

A seção deve apresentar as disciplinas atuais selecionadas no Perfil.

## RF-31 — Todas as disciplinas

O sistema deve possuir catálogo das disciplinas confirmadas do curso.

A referência acadêmica inicial do MVP será a:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Fontes institucionais:

Grade Curricular:

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

Matriz / Organograma de pré-requisitos:

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

Ela será utilizada como referência inicial para:

- disciplinas;
- semestres;
- carga horária;
- ementas;
- pré-requisitos.

A necessidade de contemplar estudantes vinculados à Matriz 2017 será verificada posteriormente e não bloqueia as atividades atuais.

> **Atualização (19/10/2026):** o catálogo foi carregado com as 49 disciplinas obrigatórias da Matriz 2023 (nome, semestre, carga horária e link oficial da ementa). As eletivas ficam fora por enquanto. Ver `decisoes.md` 53.

## RF-32 — Busca de disciplina

O usuário deve conseguir pesquisar disciplinas pelo nome.

## RF-33 — Página da disciplina

Uma disciplina pode possuir:

- informações básicas;
- link para ementa oficial;
- materiais aprovados.

O link oficial só deve aparecer como link funcional quando a URL estiver confirmada.

## RF-34 — Disciplina sem materiais

> **Atualização (04/11/2026):** todas as disciplinas têm área de materiais (`decisoes.md` 54.2). Este estado não aparece no MVP.

Se uma disciplina ainda não possuir área de materiais:

- deve continuar acessível;
- não deve oferecer envio de material;
- pode apresentar informação oficial quando disponível.

---

# 9. Materiais

## RF-35 — Compartilhar material

> **Atualização (04/11/2026):** vale para todas as disciplinas. O formulário tem também a opção "Compartilhar anonimamente", e os limites são título 200, descrição 2.000 e link 2.048 (`decisoes.md` 55.2 e 55.3). São 10 envios ou edições por hora, contando só os que deram certo.

Em disciplinas habilitadas, o usuário deve poder compartilhar material.

Campos:

- Título;
- Descrição opcional;
- Link.

## RF-36 — Material por link

No MVP, materiais são enviados exclusivamente por link.

Não existe upload de arquivo.

## RF-37 — Disciplina do material

O material deve permanecer relacionado à disciplina a partir da qual foi enviado.

O usuário não deve alterar essa disciplina no formulário.

## RF-38 — Moderação

Material enviado deve iniciar com status:

**PENDENTE**

Só pode aparecer publicamente depois de:

**APROVADO**

## RF-39 — Autoria pública

> **Atualização (02/11/2026):** substituído. O material mostra o nome de exibição do autor, e o autor pode escolher "Compartilhar anonimamente". Ver `decisoes.md` 55.2.

No MVP atual, materiais devem aparecer publicamente como:

**Compartilhado pela comunidade**

O ADMIN continua identificando o autor para:

- Moderação;
- notificação;
- controle interno do envio.

A decisão sobre apresentar ou não a autoria publicamente não será tomada neste semestre.

Ela será retomada no próximo semestre, após os primeiros testes com estudantes.

Até essa avaliação, permanece o comportamento atual:

**Compartilhado pela comunidade**

## RF-39.1 — Data pública do material

A data apresentada publicamente para um material deve corresponder à:

**data de aprovação/publicação.**

A data original de envio pode permanecer registrada internamente.

Exemplo:

Material enviado na segunda-feira  
→ permanece PENDENTE  
→ ADMIN aprova na quarta-feira  
→ publicamente aparece como publicado na quarta-feira.

---

# 10. Oportunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

## RF-40 — Fontes

A área deve separar:

- Oportunidades do IFSul;
- Oportunidades indicadas pela comunidade.

## RF-41 — Recursos institucionais

Recursos institucionais só devem possuir links clicáveis depois da confirmação da URL.

## RF-42 — Indicar oportunidade

O usuário pode enviar oportunidade com:

- Título;
- Tipo;
- Descrição opcional;
- Link.

## RF-43 — Tipos

Tipos iniciais:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

## RF-44 — Moderação

Oportunidade enviada deve ficar PENDENTE até análise do ADMIN.

## RF-45 — Data pública

Depois da aprovação, a referência pública da oportunidade deve considerar:

**data de aprovação/publicação.**

A data original de envio permanece como informação interna.

Essa regra segue o mesmo padrão utilizado para os demais conteúdos colaborativos.

---

# 11. Comunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

## RF-46 — Estrutura

A Comunidade deve possuir:

- O que está rolando;
- Encontre estudantes;
- Conecte-se com a comunidade.

## RF-47 — Feed

O feed deve utilizar ordem cronológica.

Não deve possuir no MVP:

- curtidas;
- comentários;
- seguidores;
- ranking;
- algoritmo de recomendação.

## RF-48 — Publicação

O usuário deve poder compartilhar:

- Título;
- Descrição;
- Link.

A publicação deve passar por Moderação.

## RF-48.1 — Data pública da publicação

A data apresentada publicamente para uma publicação deve corresponder à:

**data de aprovação/publicação.**

A data original de envio pode permanecer registrada internamente.

## RF-49 — Material no feed

> **Atualização (04/11/2026):** fora do MVP (`decisoes.md` 56).

Novo material aprovado pode aparecer no feed.

Ao acessar:

→ abrir a disciplina correspondente em Estudos.

---

# 12. Busca de estudantes

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

## RF-50 — Campos pesquisáveis

A busca pode considerar:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

## RF-51 — Próprio usuário

O usuário autenticado não deve aparecer nos próprios resultados da Comunidade.

## RF-52 — Ordem

Sem critério de busca que altere a listagem, estudantes devem ser apresentados em ordem alfabética.

## RF-53 — Disciplina atual

Quando um estudante for localizado por disciplina atual:

deve aparecer como:

**Cursa atualmente**

Isso não significa que ele marcou:

**Pode ajudar com**

---

# 13. Perfil público

> **Atualização (04/11/2026):** implementado sem o Semestre e sem lista de materiais. O acesso é pelo nome do autor no material, numa URL com código aleatório (`decisoes.md` 57).

## RF-54 — Acesso

Perfis públicos são visíveis somente para usuários autenticados do ConectaCC.

## RF-55 — Dados permitidos

Podem aparecer:

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

## RF-56 — Dados proibidos

Não exibir:

- e-mail institucional;
- Nome completo da conta quando diferente do Nome de exibição;
- dados de autenticação;
- dados administrativos.

## RF-57 — Campos vazios

Campos opcionais vazios devem ser omitidos.

---

# 14. Configurações

## RF-58 — Conta

Configurações deve exibir:

- E-mail institucional em modo somente leitura.

## RF-59 — Alterar senha

Usuário autenticado deve poder alterar a senha informando:

- Senha atual;
- Nova senha;
- Confirmar nova senha.

> **Atualização (04/11/2026):** a troca encerra as outras sessões da conta e mantém a atual. O limite é de 5 tentativas por minuto (`decisoes.md` 61).

## RF-60 — Validação da nova senha

A nova senha deve possuir:

- mínimo de 8 caracteres;
- confirmação igual.

## RF-61 — Exclusão da conta

Exclusão automática de conta não faz parte da interface do MVP atual.

A política deverá ser definida antes do piloto.

> **Atualização (05/11/2026):** a política foi definida em `decisoes.md` 64: a pessoa solicita por e-mail ou canal de contato, e o ADMINISTRADOR executa. Os dados pessoais são removidos ou desvinculados, as solicitações pendentes são canceladas, e os materiais aprovados continuam publicados como "Compartilhado pela comunidade". A implementação, inclusive a ação no `/admin`, fica fora do MVP.

---

# 15. Notificações

## RF-62 — Origem

O MVP deve gerar notificações para resultados da Moderação.

Tipos:

- Material aprovado;
- Material rejeitado;
- Publicação aprovada;
- Publicação rejeitada;
- Oportunidade aprovada;
- Oportunidade rejeitada.

## RF-63 — Motivo

Quando houver motivo de rejeição, ele pode aparecer na notificação.

## RF-64 — Badge

O sino deve indicar quantidade de notificações não lidas.

Visualmente:

- 1 a 9;
- 9+ acima desse limite.

## RF-65 — Leitura

> **Atualização (04/11/2026):** clicar numa notificação marca como lida e leva ao conteúdo: uma aprovação abre a disciplina, e uma rejeição abre o item em Meus conteúdos (`decisoes.md` 55.5).

Deve existir suporte para:

- marcar como lida;
- marcar todas como lidas.

O comportamento de navegação ao clicar em cada notificação ainda será definido.
Essa decisão deverá ser tomada antes da implementação definitiva de Notificações e não bloqueia a definição da arquitetura geral.

---

# 16. Moderação

## RF-66 — Acesso

> **Atualização (02/11/2026):** MODERADOR e ADMINISTRADOR acessam a Moderação (§1.2). O MODERADOR não vê o próprio envio na fila.

Somente ADMIN pode acessar Moderação.

## RF-67 — Fila única

A Moderação deve possuir uma fila única para:

- MATERIAL;
- PUBLICAÇÃO;
- OPORTUNIDADE.

## RF-68 — Status

A fila principal mostra conteúdos:

**PENDENTES**

## RF-69 — Ordem

Pendências devem ser ordenadas:

**mais antigas primeiro**

## RF-70 — Filtro

Filtros:

- Todos;
- Material;
- Publicação;
- Oportunidade.

## RF-71 — Análise

O ADMIN pode consultar:

- tipo;
- título;
- descrição;
- autor;
- data do envio;
- link;
- informações específicas do conteúdo.

## RF-72 — Aprovar

ADMIN pode aprovar conteúdo PENDENTE.

Depois:

- status → APROVADO;
- conteúdo sai da fila;
- conteúdo aparece na área correspondente;
- autor recebe notificação.

## RF-73 — Rejeitar

> **Atualização (02/11/2026):** o motivo é obrigatório para MODERADOR e ADMINISTRADOR, com até 500 caracteres. Ver `decisoes.md` 55.4.

ADMIN pode rejeitar conteúdo PENDENTE.

Motivo:

- opcional.

Depois:

- status → REJEITADO;
- conteúdo sai da fila;
- não é publicado;
- autor recebe notificação.

## RF-74 — Próprio conteúdo

> **Atualização (04/11/2026):** substituído. O ADMINISTRADOR pode moderar o próprio conteúdo. O MODERADOR não pode, e o próprio envio não aparece na fila dele (`decisoes.md` 51.2).

ADMIN não pode Aprovar ou Rejeitar conteúdo de sua própria autoria.

Outro ADMIN deverá realizar a análise.

## RF-75 — Concorrência

> **Atualização (04/11/2026):** se o autor cancelou o envio durante a análise, a mensagem é "Este conteúdo foi cancelado pelo autor." (`decisoes.md` 55.6).

Uma segunda decisão não pode sobrescrever a primeira.

Se conteúdo já tiver sido processado:

**Este conteúdo já foi analisado.**

---

# 17. Links externos

## RF-76 — Nova guia

Links externos devem abrir em nova guia.

## RF-77 — Identificação

A interface deve indicar quando o usuário será direcionado para ambiente externo.

## RF-78 — URLs

Somente URLs válidas e seguras devem ser aceitas.

A validação deve ocorrer também no servidor.

---

# 18. Estados gerais

## RF-79 — Loading

Módulos que dependem de carregamento devem possuir estado de loading.

## RF-80 — Erro

Falhas devem apresentar mensagem adequada e permitir nova tentativa quando aplicável.

## RF-81 — Estado vazio

Ausência de dados deve ser diferenciada de erro.

## RF-82 — Busca sem resultado

Busca sem correspondências deve ser diferenciada de lista vazia.

---

# 19. Responsividade

## RNF-01

A aplicação deverá ser utilizável em desktop e dispositivos móveis.

## RNF-02

No mobile, a sidebar poderá ser transformada em drawer.

## RNF-03

Cards, formulários, modais e painéis devem adaptar-se à largura disponível.

---

# 20. Acessibilidade

## RNF-04

Formulários devem possuir labels adequados.

## RNF-05

Estados de foco devem ser visíveis.

## RNF-06

Informações não devem depender somente de cor.

## RNF-07

Mensagens de erro devem estar associadas aos campos correspondentes.

## RNF-08

Modais e painéis devem possuir tratamento adequado de foco.

## RNF-09

Links externos devem ser identificados.

## RNF-10

Loading, erro e sucesso devem possuir feedback compreensível também para tecnologias assistivas.

---

# 21. Fora do escopo do MVP

Não fazem parte da primeira versão:

- chat;
- mensagens diretas;
- comentários;
- curtidas;
- reações;
- seguidores;
- matching;
- pedido de ajuda;
- IA;
- recomendações;
- ranking;
- gamificação;
- upload de materiais;
- upload de fotos;
- confirmação de propriedade do e-mail institucional por link ou código;
- candidatura interna;
- CRUD administrativo completo;
- histórico administrativo completo;
- remoção de conteúdo aprovado.

Esses itens poderão ser avaliados posteriormente.
