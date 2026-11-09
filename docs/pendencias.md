# Pendências e decisões em aberto - ConectaCC

Este documento reúne somente decisões, validações e atividades que ainda precisam ser concluídas no ConectaCC.

O objetivo é diferenciar:

- decisões de produto ainda abertas;
- validações técnicas ainda necessárias;
- pendências obrigatórias antes do piloto;
- informações institucionais ainda a avaliar;
- possibilidades de evolução pós-MVP.

Decisões já concluídas não devem permanecer neste documento como pendências. Elas ficam registradas em `decisoes.md` e nas demais documentações correspondentes.
---

# 1. Decisões e próximos passos de produto

A maior parte das decisões necessárias para o MVP já foi definida.

Permanecem abertas apenas questões que não bloqueiam a versão atual ou que deverão ser retomadas conforme os resultados do piloto.

---

## 1.1 Recursos institucionais adicionais

O MVP já possui acessos institucionais distribuídos entre Home, Estudos e Oportunidade.

Ainda poderá ser avaliada futuramente a inclusão ou melhor contextualização de recursos como:

- Painel de Sistemas do Câmpus;
- informações sobre professores;
- organograma institucional;
- outros serviços identificados posteriormente.

A decisão deverá seguir o princípio:

> O ConectaCC deve conectar e contextualizar recursos existentes, e não reconstruir sistemas institucionais.

Esses itens não bloqueiam o piloto.

---

## 1.2 Catálogo acadêmico — evoluções futuras

O catálogo atual utiliza como referência a Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.

As 49 disciplinas obrigatórias confirmadas estão cadastradas e possuem área de materiais.

Continuam para avaliação futura:

- disciplinas eletivas;
- estudantes ainda vinculados à Matriz 2017;
- necessidade de suportar mais de uma matriz curricular simultaneamente.

Esses itens não bloqueiam o piloto atual.

---

## 1.3 Visibilidade do Perfil — evolução futura

O Perfil público atual é visível apenas para usuários autenticados.

Futuramente poderão ser avaliadas opções adicionais de privacidade.

### Ocultar disciplinas atuais

Antes de implementar, definir:

- se o usuário poderá esconder todas ou apenas algumas disciplinas;
- se quem esconde as próprias disciplinas poderá continuar vendo as dos outros;
- como isso afetará futuras buscas por disciplina;
- se o usuário continuará aparecendo em resultados relacionados à disciplina.

### Perfil oculto ou participação na descoberta

Antes de implementar, definir:

- se o Perfil inteiro poderá ser ocultado ou apenas determinados campos;
- quem ainda poderá acessá-lo;
- o que acontece com conteúdos identificados já publicados;
- como ficam futuras funcionalidades de busca de estudantes;
- como ficam "Posso ajudar com" e "Procuro ajuda com";
- quais funcionalidades deixam de estar disponíveis quando o usuário optar por não participar da descoberta social.

### Semestre no Perfil

A inclusão do semestre poderá ser reavaliada futuramente junto das demais regras de Perfil e visibilidade.

---

## 1.4 Aviso por e-mail após alteração de senha

O MVP atual não envia aviso por e-mail quando a senha é alterada.

Futuramente deverá ser avaliado se o usuário receberá uma mensagem informando que ocorreu uma alteração de senha.

Essa funcionalidade depende de existir um serviço de e-mail configurado de forma confiável em produção.

---

# 2. Validações técnicas ainda necessárias

As principais decisões de arquitetura e implementação já foram concluídas.

Permanecem principalmente validações finais antes do piloto.

---

## 2.1 Limites dos campos do Perfil

O backend utiliza atualmente os seguintes limites:

- Nome de exibição: 120 caracteres;
- Sobre mim: 5.000 caracteres;
- até 50 itens por campo de tags;
- cada tag: até 255 caracteres.

Esses valores funcionam atualmente como limites técnicos.

Ainda poderá ser avaliado, a partir do uso e dos testes com estudantes, se devem permanecer como limites definitivos de Produto ou ser reduzidos para melhorar a experiência da interface.

Essa decisão não bloqueia o piloto.

Os demais limites principais já estão definidos:

### Material

- título: 200 caracteres;
- descrição: 2.000 caracteres;
- link: 2.048 caracteres;
- motivo da rejeição: 500 caracteres.

### Oportunidade

- título: 200 caracteres;
- descrição: 300 caracteres;
- link: 2.048 caracteres.

---

## 2.2 Acessibilidade, foco e navegação

Antes do piloto, validar de forma sistemática:

- navegação por teclado;
- foco visível;
- foco preso em modais quando necessário;
- retorno do foco após fechamento;
- associação de mensagens de erro aos campos;
- funcionamento dos controles em leitores e tecnologias assistivas;
- estados que não dependem somente de cor;
- comportamento de alterações não salvas.

---

## 2.3 Loading, erro e estados vazios

Realizar uma revisão final dos estados de:

- loading;
- erro;
- estado vazio;
- busca sem resultado;
- ações em andamento;
- sucesso.

A validação deve contemplar as principais áreas do MVP, incluindo:

- Home;
- Perfil;
- Estudos;
- Materiais;
- Oportunidade;
- Comunidade;
- Meus conteúdos;
- Configurações;
- Moderação;
- Notificações.

---

# 3. Pendências obrigatórias antes do piloto

## 3.1 Revisar os Termos de participação

A versão atual dos Termos deverá ser revisada antes da entrada dos participantes do piloto.

A revisão deve deixar claro, quando aplicável:

- quais informações do Perfil ficam visíveis para outros usuários autenticados;
- que as disciplinas atuais fazem parte das informações visíveis do Perfil;
- como funciona a autoria dos materiais;
- como funciona o compartilhamento anônimo de materiais;
- que oportunidades não exibem publicamente quem realizou o envio;
- finalidade dos dados coletados;
- contexto e finalidade do período de testes;
- política relacionada à exclusão da conta e tratamento dos conteúdos já enviados.

A redação final deverá ser avaliada considerando a LGPD e as orientações institucionais aplicáveis.

Sempre que houver alteração relevante nos Termos, uma nova versão deverá ser criada.

---

## 3.2 Exclusão de conta e dados

A política geral já foi definida em `decisoes.md` 64, mas ainda existem pontos operacionais a confirmar antes do piloto.

Definir:

- canal pelo qual o usuário poderá solicitar exclusão;
- quem será responsável por receber e executar a solicitação;
- como o pedido será atendido enquanto não existir autoexclusão pela interface;
- retenção necessária de dados;
- tratamento de materiais rejeitados, cancelados ou anteriormente excluídos;
- tratamento das oportunidades vinculadas à conta;
- procedimento para eventual solicitação relacionada à LGPD.

A autoexclusão pela interface não faz parte do MVP atual.

---

## 3.3 Orientação para motivos de rejeição

O motivo da rejeição é obrigatório para MODERADOR e ADMINISTRADOR e possui limite de 500 caracteres.

Antes do piloto, preparar uma orientação simples para quem realizará a Moderação.

O motivo deve ser:

- objetivo;
- compreensível;
- explicativo quando necessário;
- respeitoso;
- relacionado ao conteúdo analisado;
- sem informações inadequadas ou desnecessárias.

---

# 4. Recursos institucionais ainda a avaliar

Os principais recursos necessários para o MVP já foram identificados e os acessos utilizados na aplicação estão documentados nas respectivas decisões.

Ainda poderão ser avaliados:

- [ ] informações ou acesso relacionado aos professores;
- [ ] necessidade de acesso direto ao Painel de Sistemas do Câmpus;
- [ ] grupo de WhatsApp dos estudantes, caso exista autorização e necessidade de disponibilizá-lo;
- [ ] outros recursos institucionais identificados durante o piloto.

Novos links somente devem ser adicionados depois da confirmação da fonte e do endereço oficial.

Esses itens não bloqueiam o início do piloto.

---

# 5. Preparação da validação

## Configuração já definida

- [x] público do piloto definido como estudantes de Ciência da Computação do IFSul – Câmpus Passo Fundo;
- [x] piloto planejado para 12 participantes;
- [x] composição planejada: 10 ALUNOS e 2 ADMINISTRADORES;
- [x] ADMINISTRADORES definidos: Marcelo e Viviane;
- [x] domínio institucional confirmado: `academico.ifsul.edu.br`;
- [x] cadastro fechado por lista de convidados disponível através de `ALLOWED_EMAILS`.

## Antes do início dos testes

- [ ] selecionar os 10 estudantes participantes;
- [ ] incluir os 12 participantes em `ALLOWED_EMAILS`;
- [ ] revisar e publicar a versão final dos Termos de participação do piloto;
- [ ] definir o canal e o procedimento para solicitações de exclusão de conta;
- [ ] realizar verificação final dos links institucionais utilizados;
- [ ] preparar formulário ou instrumento de feedback;
- [ ] definir o que será observado durante os testes;
- [ ] validar o ambiente de produção;
- [ ] executar a rodada final de testes funcionais;
- [ ] validar responsividade;
- [ ] validar acessibilidade;
- [ ] corrigir problemas bloqueadores identificados antes da entrada dos participantes.
---

# 6. Pós-MVP

As funcionalidades abaixo não fazem parte da primeira versão, mas poderão ser avaliadas após o piloto.

## Conta e autenticação

- recuperação de senha;
- confirmação de propriedade do e-mail institucional por link ou código;
- aviso por e-mail após alteração de senha;
- autoexclusão de conta pela interface;
- expansão para outros domínios institucionais.

## Perfil e descoberta de estudantes

- controles adicionais de visibilidade do Perfil;
- ocultação de disciplinas;
- opção de não participar da descoberta social;
- busca de estudantes;
- busca de estudantes por disciplina;
- inclusão do semestre no Perfil;
- upload de foto.

## Comunidade e interação

- canais da comunidade;
- publicação externa como tipo próprio de conteúdo;
- comentários;
- curtidas;
- reações;
- seguidores;
- pedido de ajuda;
- matching entre estudantes;
- chat;
- mensagens diretas.

## Materiais e oportunidades

- upload direto de arquivos;
- edição de oportunidade aprovada;
- retirada administrativa de conteúdo publicado;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão de conteúdo;
- histórico mais detalhado de versões e alterações.

## Notificações

Poderão ser avaliados:

- histórico completo de notificações;
- exclusão de notificações;
- expiração;
- filtros;
- paginação ou "Mostrar mais";
- preferências de notificação;
- novas notificações relacionadas a funcionalidades futuras.

## Administração e auditoria

A auditoria das ações que alteram dados já faz parte do sistema.

Continuam como possíveis evoluções:

- auditoria de consultas;
- auditoria de Login;
- exportação de eventos;
- definição de política final de retenção dos eventos;
- histórico administrativo mais detalhado;
- contador de pendências na Moderação.

## Outras evoluções

- recomendações personalizadas;
- Inteligência Artificial;
- gamificação.
---

# 7. Observação

Este documento deve representar apenas o que ainda está realmente pendente.

Sempre que uma decisão for concluída ou uma funcionalidade for implementada:

- a decisão definitiva deve ser registrada em `decisoes.md` ou na documentação correspondente;
- o item deve ser removido desta lista de pendências;
- não é necessário manter aqui o histórico de decisões já encerradas.

As pendências atuais não significam necessariamente problemas no MVP.

Algumas representam requisitos obrigatórios antes do piloto, enquanto outras são melhorias ou decisões que poderão ser avaliadas somente após os testes com estudantes.
