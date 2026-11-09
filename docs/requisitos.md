# Requisitos do MVP - ConectaCC

Este documento registra os principais requisitos funcionais e regras de negócio do MVP do ConectaCC.

O objetivo é servir como referência para:

- implementação;
- testes;
- revisão funcional;
- validação do produto.

Os requisitos descritos aqui representam decisões de produto e não definem, por si só, a tecnologia que deverá ser utilizada.

O documento descreve os requisitos da versão atual do MVP.

Oportunidade e Comunidade fazem parte do escopo atual.

Funcionalidades como busca de estudantes, publicações como tipo próprio de conteúdo, canais da comunidade e outras evoluções futuras permanecem fora do MVP.

---

# 1. Papéis de usuário

O MVP possui três papéis:

- ALUNO;
- MODERADOR;
- ADMINISTRADOR.

Representação conceitual do nível de acesso:

ALUNO  
→ MODERADOR  
→ ADMINISTRADOR

Essa representação não significa herança técnica automática. As permissões são controladas pelas regras de autorização da aplicação.

## 1.1 ALUNO

O usuário ALUNO pode:

- criar conta;
- realizar Login;
- acessar Home;
- editar o próprio Perfil;
- selecionar disciplinas atuais;
- acessar Estudos;
- compartilhar materiais;
- acessar Oportunidade;
- indicar oportunidades;
- acessar Comunidade;
- consultar Perfis públicos;
- consultar e gerenciar os próprios conteúdos;
- acessar Configurações;
- alterar a própria senha;
- receber notificações.

O usuário ALUNO não pode:

- acessar a área de Moderação;
- consultar conteúdos pendentes de outros usuários;
- aprovar conteúdos;
- rejeitar conteúdos;
- acessar o painel `/admin`;
- visualizar informações administrativas;
- alterar papéis de usuários.

## 1.2 MODERADOR

O MODERADOR possui as funcionalidades comuns do usuário e também pode:

- acessar a Moderação;
- analisar Materiais;
- analisar Oportunidades;
- aprovar conteúdos pendentes;
- rejeitar conteúdos pendentes;
- informar o motivo obrigatório da rejeição.

### Regras

- somente ADMINISTRADOR pode atribuir o papel MODERADOR;
- a Moderação fica dentro da aplicação comum;
- o MODERADOR não acessa `/admin`;
- o MODERADOR não altera papéis;
- o MODERADOR não gerencia permissões administrativas;
- o MODERADOR não pode moderar o próprio conteúdo;
- o próprio conteúdo não aparece entre as pendências que ele pode analisar;
- o MODERADOR não visualiza a identidade real do autor de material enviado anonimamente.

## 1.3 ADMINISTRADOR

O ADMINISTRADOR possui as funcionalidades comuns do usuário e também pode:

- acessar a Moderação;
- moderar o próprio conteúdo;
- acessar o painel `/admin`;
- consultar usuários;
- alterar o papel de outras contas;
- consultar registros administrativos de Materiais e Oportunidades;
- acessar a Auditoria;
- identificar o autor real de material anônimo nas áreas administrativas autorizadas.

O ADMINISTRADOR não pode alterar o próprio papel.

Nas áreas públicas, materiais anônimos continuam aparecendo como:

**Compartilhado pela comunidade**

A identificação do autor real fica restrita às áreas administrativas autorizadas.

## Evoluções futuras

Não fazem parte do MVP atual:

- retirada administrativa de conteúdo publicado;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão de conteúdo publicado.

As regras já definidas para essas funcionalidades devem ser aplicadas quando entrarem no escopo.

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

No MVP e no piloto, o cadastro deve utilizar e-mail do domínio:

**`academico.ifsul.edu.br`**

O sistema deve validar:

- formato válido do e-mail;
- domínio institucional permitido.

O domínio aceito pode ser configurado através de `ALLOWED_EMAIL_DOMAINS`.

A possibilidade de aceitar outras instituições fica fora do MVP atual.

## RF-05.1 — Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

O usuário não precisa confirmar a propriedade do endereço antes de utilizar a conta.

Essa funcionalidade poderá ser avaliada futuramente.

## RF-05.2 — Cadastro fechado do piloto

Durante o período de teste, o cadastro pode ser restringido a uma lista de pessoas convidadas.

Quando `ALLOWED_EMAILS` possuir registros:

- o e-mail deve pertencer ao domínio institucional permitido;
- o e-mail também deve estar presente na lista de convidados;
- somente pessoas convidadas podem criar novas contas.

Quando a lista estiver vazia:

- o cadastro fica disponível para qualquer endereço pertencente ao domínio institucional permitido.

Uma pessoa não convidada deve receber uma mensagem genérica informando que o cadastro está restrito ao período de teste.

A validação da lista de convidados deve ocorrer antes da verificação que poderia revelar se o e-mail já possui conta.
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

A recuperação de senha não faz parte do MVP atual.

Caso seja implementada futuramente, deverá existir um fluxo seguro de recuperação utilizando o e-mail institucional, sem revelar se determinado endereço possui ou não uma conta.

Também deverão ser definidos:

- validade do link;
- segurança do token;
- provedor de e-mail;
- encerramento das demais sessões após a redefinição.

A implementação futura deve manter a mesma política mínima de senha utilizada pelo cadastro.

---

# 5. Home

## RF-13 — Home autenticada

Depois do Login, o usuário deve acessar a Home.

## RF-14 — Saudação

A Home deve apresentar saudação utilizando o Nome de exibição.

## RF-15 — Atalhos principais

A Home deve oferecer acesso às principais áreas:

- Estudos;
- Oportunidade;
- Comunidade;
- Perfil.

## RF-15.1 — Acessos institucionais

A Home deve disponibilizar acessos institucionais confirmados e relevantes para o estudante.

A versão atual possui acesso a:

- IFSul;
- Informática / Ciência da Computação;
- SUAP;
- Moodle;
- Instagram do IFSul Passo Fundo;
- CORAC.

Links externos devem direcionar para as fontes correspondentes sem reproduzir o conteúdo institucional dentro do ConectaCC.

## RF-16 — Perfil incompleto

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

Enquanto estiver incompleto, a Home deve apresentar:

**Complete seu perfil**

## RF-17 — Perfil completo

Depois que o Perfil estiver completo:

- o card "Complete seu perfil" deve desaparecer;
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

O sistema deve possuir o catálogo das disciplinas obrigatórias confirmadas da:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

O catálogo atual contém 49 disciplinas obrigatórias e utiliza informações institucionais de:

- nome;
- semestre;
- carga horária;
- ementa oficial.

A necessidade de contemplar estudantes vinculados à Matriz 2017 poderá ser avaliada futuramente.

## RF-32 — Busca de disciplina

O usuário deve conseguir pesquisar disciplinas pelo nome.

## RF-33 — Página da disciplina

Cada disciplina pode apresentar:

- informações acadêmicas;
- carga horária;
- link para ementa oficial;
- materiais aprovados;
- opção para compartilhar material.

O link da ementa somente deve ser disponibilizado quando houver URL institucional confirmada.

## RF-34 — Área de materiais

Todas as disciplinas cadastradas da Matriz 2023 devem possuir área de materiais.

Uma disciplina sem materiais aprovados deve continuar acessível e apresentar o estado vazio correspondente.

## RF-34.1 — Recursos acadêmicos

Estudos deve disponibilizar acessos aos recursos acadêmicos confirmados:

- Biblioteca Virtual;
- Calendário Acadêmico.

Esses recursos devem abrir suas respectivas fontes externas.
---

# 9. Materiais

## RF-35 — Compartilhar material

O usuário deve poder compartilhar material em qualquer disciplina cadastrada.

Campos:

- Título;
- Descrição opcional;
- Link;
- opção "Compartilhar anonimamente".

Limites:

- Título: 200 caracteres;
- Descrição: 2.000 caracteres;
- Link: 2.048 caracteres.

## RF-36 — Material por link

No MVP, materiais são enviados exclusivamente por link.

Não existe upload direto de arquivos.

## RF-37 — Disciplina do material

O material deve permanecer relacionado à disciplina a partir da qual foi enviado.

O usuário não altera a disciplina no formulário de envio.

## RF-38 — Moderação

Material enviado deve iniciar com status:

**PENDENTE**

Só pode aparecer publicamente depois de:

**APROVADO**

A análise pode ser realizada por MODERADOR ou ADMINISTRADOR, respeitando as regras de conflito de interesse.

## RF-39 — Autoria pública

Por padrão, um material aprovado deve apresentar o **Nome de exibição** do autor.

O nome pode direcionar ao Perfil público.

Quando o usuário selecionar:

**Compartilhar anonimamente**

o material deve aparecer publicamente como:

**Compartilhado pela comunidade**

Nesse caso:

- o sistema continua registrando internamente o autor;
- o MODERADOR não visualiza a identidade real;
- o ADMINISTRADOR pode consultar a identidade apenas nas áreas administrativas autorizadas.

## RF-39.1 — Data pública do material

A data apresentada publicamente deve corresponder à:

**data de aprovação/publicação.**

A data original do envio permanece registrada internamente.

## RF-39.2 — Link duplicado

O sistema não deve aceitar um novo material com o mesmo link já existente como PENDENTE ou APROVADO na mesma disciplina.

## RF-39.3 — Cancelamento

O autor pode cancelar um material enquanto ele estiver PENDENTE.

Depois do cancelamento:

- ele deixa a fila de Moderação;
- não pode ser publicado;
- continua disponível para consulta do autor em Meus conteúdos.

## RF-39.4 — Edição de material aprovado

O autor pode solicitar alteração de material APROVADO.

A edição deve gerar uma nova solicitação de Moderação.

Enquanto a alteração estiver PENDENTE:

- a versão anteriormente aprovada continua pública.

Depois da aprovação:

- a nova versão substitui a versão pública anterior.

Depois da rejeição:

- a versão pública anterior permanece disponível;
- o autor recebe notificação sobre a rejeição da alteração.

## RF-39.5 — Exclusão de material aprovado

O autor pode excluir material aprovado.

Depois da exclusão:

- ele deixa de aparecer publicamente em Estudos e na Comunidade;
- o registro continua disponível em Meus conteúdos e para os controles internos necessários.

---

# 10. Oportunidade

## RF-40 — Fontes

A área Oportunidade deve separar:

- recursos institucionais do IFSul;
- oportunidades indicadas pela comunidade.

## RF-41 — Recursos institucionais

Somente links institucionais confirmados devem ser disponibilizados.

A versão atual possui acesso a:

- Bolsas e programas;
- Projetos e estágios.

## RF-42 — Indicar oportunidade

O usuário pode indicar uma oportunidade informando:

- Tipo;
- Título;
- Descrição curta;
- Link;
- Modalidade;
- Inscrições até, opcional.

Limites:

- Título: 200 caracteres;
- Descrição: 300 caracteres;
- Link: 2.048 caracteres.

## RF-43 — Tipos

Tipos disponíveis:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

## RF-43.1 — Modalidade

A modalidade é obrigatória.

Valores:

- Remoto;
- Híbrido;
- Presencial.

## RF-44 — Moderação

Oportunidade enviada deve iniciar como:

**PENDENTE**

A análise pode ser realizada por MODERADOR ou ADMINISTRADOR.

A oportunidade só pode aparecer publicamente depois de aprovada.

## RF-45 — Data pública

Depois da aprovação, a referência pública deve utilizar:

**data de aprovação/publicação.**

A data original do envio permanece registrada internamente.

## RF-45.1 — Prazo

O campo **Inscrições até** é opcional.

Quando houver uma data informada, ela deve ser igual ou posterior ao dia do envio.

Quando o prazo não for informado, a oportunidade permanece visível por 15 dias após a aprovação.

## RF-45.2 — Autoria pública

A identidade da pessoa que indicou uma oportunidade não deve ser exibida publicamente.

A oportunidade deve aparecer como contribuição da comunidade.

## RF-45.3 — Link duplicado

O sistema não deve aceitar nova oportunidade utilizando o mesmo link de outra oportunidade PENDENTE ou APROVADA.

## RF-45.4 — Edição

A edição de oportunidade aprovada não faz parte do MVP atual.

---

# 11. Comunidade

## RF-46 — Estrutura

A Comunidade funciona como mural de descoberta de conteúdos aprovados.

O mural reúne:

- Materiais;
- Oportunidades.

Publicações como tipo próprio de conteúdo não fazem parte do MVP atual.

## RF-47 — Ordenação

O usuário deve conseguir ordenar o mural por:

- Mais recentes;
- Mais antigos.

## RF-48 — Busca

O usuário deve conseguir pesquisar conteúdos no mural.

A busca deve considerar informações relevantes dos conteúdos, como:

- título;
- disciplina do material;
- nome do autor identificado, quando aplicável.

## RF-48.1 — Filtro por tipo

O mural deve possuir filtro por tipo:

- Todos os tipos;
- Materiais;
- Oportunidades.

O filtro deve ser utilizado no lugar de abas separadas.

## RF-48.2 — Visualização

O usuário deve poder alternar entre:

- lista;
- grade.

No mobile, a aplicação pode utilizar somente a visualização adequada ao espaço disponível.

## RF-49 — Material no mural

Material aprovado pode aparecer no mural.

Quando identificado:

- pode apresentar o Nome de exibição do autor;
- pode permitir acesso ao Perfil público.

Quando anônimo:

- deve aparecer como compartilhado pela comunidade;
- não deve revelar o autor.

## RF-49.1 — Oportunidade no mural

Oportunidade aprovada pode aparecer no mural.

A identidade de quem realizou a indicação não deve ser exibida publicamente.

## RF-49.2 — Mostrar mais

Quando existirem mais conteúdos do que os apresentados inicialmente, deve existir opção:

**Mostrar mais**

## RF-49.3 — Compartilhar conteúdo

A Comunidade deve oferecer uma opção para contribuir com:

- Material;
- Oportunidade.

Ao escolher Material:

→ o usuário deve ser direcionado para Estudos, onde seleciona a disciplina.

Ao escolher Oportunidade:

→ deve ser aberto o formulário de envio da oportunidade.

## RF-49.4 — Comunidade em números

A Comunidade pode apresentar indicadores gerais relacionados a:

- Materiais;
- Oportunidades;
- Estudantes.

Esses números são informativos e não representam ranking ou gamificação.

---

# 12. Busca de estudantes

A busca direta de estudantes não faz parte do MVP atual.

Poderá ser avaliada futuramente considerando informações como:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

Antes da implementação deverão ser definidas também as regras de privacidade, visibilidade e participação na descoberta social.

---

# 13. Perfil público

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

A autoexclusão de conta pela interface não faz parte do MVP atual.

A política de Produto prevê que a solicitação seja realizada por canal de contato definido para o projeto.

Quando uma conta for excluída:

- dados pessoais devem ser removidos ou desvinculados conforme a política definida;
- solicitações pendentes devem ser canceladas;
- materiais aprovados que precisarem permanecer disponíveis devem passar a aparecer como **Compartilhado pela comunidade**.

O procedimento operacional e sua adequação às orientações institucionais e à LGPD devem estar definidos antes do piloto.

---

# 15. Notificações

## RF-62 — Eventos

O MVP deve gerar notificações para:

- Material aprovado;
- Material rejeitado;
- Alteração de material aprovada;
- Alteração de material rejeitada;
- Oportunidade aprovada;
- Oportunidade rejeitada.

## RF-63 — Motivo da rejeição

O motivo da rejeição não precisa ser exibido diretamente na notificação.

Ele deve permanecer disponível ao autor nos detalhes do envio em Meus conteúdos.

## RF-64 — Badge

O sino deve indicar a quantidade de notificações não lidas.

Visualmente:

- 1 a 9;
- `9+` acima desse limite;
- nenhum número quando não existirem notificações não lidas.

## RF-65 — Leitura

Deve existir suporte para:

- marcar uma notificação como lida;
- marcar todas como lidas.

Abrir apenas o menu de notificações não deve marcar automaticamente os itens como lidos.

## RF-65.1 — Clique

Ao clicar em uma notificação:

- ela deve ser marcada como lida;
- o contador deve ser atualizado;
- o usuário deve ser direcionado ao destino correspondente.

Destinos atuais:

Material aprovado  
→ disciplina correspondente em Estudos.

Material rejeitado  
→ item correspondente em Meus conteúdos.

Alteração de material aprovada ou rejeitada  
→ item correspondente em Meus conteúdos.

Oportunidade aprovada  
→ oportunidade correspondente.

Oportunidade rejeitada  
→ item correspondente em Meus conteúdos.

## RF-65.2 — Ordem

As notificações devem ser apresentadas da mais recente para a mais antiga.

---

# 16. Moderação

## RF-66 — Acesso

A Moderação pode ser acessada por:

- MODERADOR;
- ADMINISTRADOR.

ALUNO não deve possuir acesso à área nem às operações protegidas de Moderação.

## RF-67 — Fila única

A Moderação deve utilizar uma fila única para:

- MATERIAL;
- OPORTUNIDADE.

Publicação não faz parte da fila atual.

## RF-68 — Status

A fila principal deve apresentar conteúdos:

**PENDENTES**

## RF-69 — Ordem

As pendências devem ser apresentadas:

**mais antigas primeiro**

## RF-70 — Filtro

A versão atual não exige filtro por tipo.

Materiais e Oportunidades permanecem na mesma fila.

## RF-71 — Análise

A pessoa responsável pela Moderação deve conseguir consultar as informações necessárias para a decisão, incluindo:

- tipo do conteúdo;
- título;
- descrição;
- autor, quando permitido;
- data do envio;
- link;
- disciplina, quando for Material;
- tipo e modalidade, quando for Oportunidade;
- demais informações específicas do conteúdo.

A autoria de material anônimo deve respeitar as permissões de cada papel.

## RF-72 — Aprovar

MODERADOR ou ADMINISTRADOR podem aprovar conteúdo PENDENTE quando possuírem permissão para analisá-lo.

Depois da aprovação:

- status → APROVADO;
- conteúdo sai da fila;
- conteúdo aparece nas áreas correspondentes;
- autor recebe notificação.

Material aprovado:

→ Estudos  
→ Comunidade.

Oportunidade aprovada:

→ Oportunidade  
→ Comunidade.

## RF-73 — Rejeitar

MODERADOR ou ADMINISTRADOR podem rejeitar conteúdo PENDENTE quando possuírem permissão para analisá-lo.

O motivo da rejeição é:

- obrigatório;
- limitado a 500 caracteres.

Depois da rejeição:

- status → REJEITADO;
- conteúdo sai da fila;
- conteúdo não é publicado;
- autor recebe notificação;
- motivo fica disponível em Meus conteúdos.

## RF-74 — Próprio conteúdo

O MODERADOR não pode moderar o próprio conteúdo.

O próprio envio não deve aparecer entre as pendências que ele pode analisar.

O ADMINISTRADOR pode moderar o próprio conteúdo.

## RF-75 — Concorrência

Uma segunda decisão não pode sobrescrever a primeira.

Se o conteúdo já tiver sido analisado, o sistema deve informar:

**Este conteúdo já foi analisado.**

Se o autor tiver cancelado o conteúdo durante a análise, o sistema deve informar:

**Este conteúdo foi cancelado pelo autor.**

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

- recuperação de senha;
- confirmação de propriedade do e-mail institucional por link ou código;
- busca de estudantes;
- canais da comunidade;
- publicação externa como tipo próprio de conteúdo;
- chat;
- mensagens diretas;
- comentários;
- curtidas;
- reações;
- seguidores;
- matching;
- pedido de ajuda;
- Inteligência Artificial;
- recomendações personalizadas;
- ranking;
- gamificação;
- upload direto de materiais;
- upload de foto de Perfil;
- candidatura a oportunidades dentro do sistema;
- edição de oportunidade aprovada;
- envio anônimo de oportunidade;
- feed algorítmico;
- rede social completa;
- autoexclusão de conta pela interface;
- CRUD administrativo completo;
- retirada administrativa de conteúdo aprovado;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão de conteúdo;
- auditoria de consultas e Login;
- exportação de eventos de auditoria.

Esses itens poderão ser avaliados posteriormente, principalmente a partir dos resultados do piloto.
---

# 22. Meus conteúdos

## RF-83 — Acesso

O usuário autenticado deve conseguir consultar os conteúdos enviados por ele em:

**Perfil → Meus conteúdos**

## RF-84 — Tipos

A área deve separar:

- Materiais;
- Oportunidades.

## RF-85 — Situação

O usuário deve conseguir acompanhar a situação dos próprios envios.

A visualização deve diferenciar os estados aplicáveis a cada tipo de conteúdo.

## RF-86 — Material rejeitado

Quando um Material for rejeitado:

- o motivo deve ficar disponível ao autor;
- o conteúdo não deve aparecer publicamente.

## RF-87 — Material pendente

O autor pode cancelar um Material PENDENTE.

## RF-88 — Material aprovado

O autor pode:

- solicitar edição de Material APROVADO;
- excluir Material APROVADO.

A edição deve seguir novamente o fluxo de Moderação.

## RF-89 — Oportunidade pendente

O autor pode cancelar uma Oportunidade PENDENTE.

## RF-90 — Oportunidade aprovada

O autor pode excluir uma Oportunidade APROVADA.

A edição de Oportunidade não faz parte do MVP atual.

## RF-91 — Paginação

Quando houver mais conteúdos do que os apresentados inicialmente, deve existir:

**Mostrar mais**
---
# 23. Painel administrativo

## RF-92 — Acesso

O painel `/admin` é exclusivo do ADMINISTRADOR.

MODERADOR e ALUNO não devem possuir acesso.

## RF-93 — Usuários

O ADMINISTRADOR deve poder:

- consultar usuários;
- buscar por nome, Nome de exibição ou e-mail;
- filtrar por papel;
- alterar o papel de outras contas.

O ADMINISTRADOR não pode alterar o próprio papel.

O painel não deve criar, editar ou excluir contas comuns diretamente.

## RF-94 — Registros de Moderação

O painel deve permitir consultar registros administrativos relacionados aos conteúdos moderados.

A consulta pode apresentar informações como:

- conteúdo;
- situação;
- autor;
- indicação de envio anônimo;
- responsável pela decisão;
- data da decisão;
- motivo da rejeição;
- informações de alterações de Material.

A aprovação e rejeição continuam sendo realizadas na área de Moderação da aplicação comum.

## RF-95 — Oportunidades

O painel deve permitir consultar as Oportunidades cadastradas e suas informações administrativas.

A consulta administrativa não substitui o fluxo de Moderação.

## RF-96 — Voltar ao ConectaCC

O painel deve oferecer uma forma de retornar à aplicação comum do ConectaCC.
---
# 24. Auditoria

## RF-97 — Acesso

A Auditoria é exclusiva do ADMINISTRADOR.

## RF-98 — Registro

Ações que alteram dados relevantes devem gerar evento de Auditoria.

O evento deve registrar informações suficientes para identificar:

- ação realizada;
- entidade afetada;
- responsável pela ação;
- data e hora.

## RF-99 — Imutabilidade

Eventos de Auditoria não devem ser alterados ou excluídos pelas operações normais da aplicação.

## RF-100 — Consulta

O ADMINISTRADOR deve conseguir consultar os eventos através do painel administrativo.

A consulta deve permitir filtros apropriados, incluindo:

- ação;
- pessoa;
- período.

## RF-101 — Conteúdo anônimo

Quando necessário para fins administrativos, a Auditoria pode registrar o autor real de material anônimo.

Essa informação deve permanecer restrita ao ADMINISTRADOR.

## RF-102 — Dados sensíveis

Eventos de Auditoria não devem registrar:

- senha;
- hash de senha;
- token;
- IP quando não houver necessidade definida;
- motivo da rejeição;
- outros segredos ou dados sensíveis desnecessários.
