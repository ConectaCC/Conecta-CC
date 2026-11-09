# Plano de Implementação - ConectaCC

Este documento organiza as principais etapas de implementação do ConectaCC e registra o andamento das funcionalidades do MVP.

O protótipo definiu a base inicial das principais telas e fluxos.

A arquitetura técnica já foi definida e o ConectaCC possui uma versão funcional em preparação para o período de testes.
---

# 1. Situação atual

O ConectaCC já possui uma versão funcional do MVP.

Atualmente estão implementados, entre outros:

- autenticação e controle de acesso;
- Home;
- Estudos;
- materiais;
- Oportunidade;
- Comunidade;
- Perfil;
- Perfil público;
- Meus conteúdos;
- Moderação;
- Notificações;
- Configurações;
- painel administrativo;
- auditoria.

O sistema utiliza os papéis:

- ALUNO;
- MODERADOR;
- ADMINISTRADOR.

O estado detalhado e atualizado da implementação deve ser acompanhado em:

`status-mvp.md`

As próximas etapas concentram-se na revisão do MVP, testes, correções, preparação do ambiente e realização do piloto com estudantes.

---

# 2. Arquitetura técnica

A arquitetura técnica do ConectaCC já foi definida.

As decisões relacionadas a:

- stack;
- arquitetura geral;
- frontend;
- backend;
- banco de dados;
- autenticação;
- autorização;
- hospedagem;
- deploy;
- estratégia de testes;
- organização do código;

estão registradas na documentação técnica do projeto.

Ver:

`stack-e-decisoes-tecnicas.md`

Requisitos de Produto não devem ser alterados silenciosamente por decisões técnicas.

Quando uma necessidade técnica afetar comportamento, escopo, experiência do usuário, permissões ou regras de negócio, ela deve ser discutida com a equipe antes da alteração do Produto.

# 3. Fundação do projeto

- [x] Definir stack.
- [x] Definir arquitetura.
- [x] Definir estrutura de pastas do código.
- [x] Configurar ambiente de desenvolvimento.
- [x] Configurar banco de dados.
- [x] Configurar autenticação.
- [x] Configurar papéis ALUNO, MODERADOR e ADMINISTRADOR.
- [x] Implementar autorização no backend para áreas e ações protegidas.
- [x] Configurar variáveis de ambiente.
- [x] Definir estratégia de branches e commits.
- [x] Definir ambiente de hospedagem.
- [x] Definir estratégia de deploy.

Detalhes da fundação: `docs/specs/2026-10-12-fundacao-design.md` e `docs/operacao.md`.

---

# 4. Autenticação

Implementado:

- [x] Login.
- [x] Cadastro.
- [x] validação do formato do e-mail.
- [x] validação do domínio institucional do IFSul.
- [x] restrição do cadastro aos convidados durante o período de teste.
- [x] Termos de participação versionados.
- [x] registro do aceite dos Termos após a criação da conta.
- [x] Logout.
- [x] proteção de rotas autenticadas.
- [x] alteração de senha em Configurações.
- [x] encerramento das demais sessões após alteração de senha.

## E-mail institucional

No MVP e no piloto, o cadastro utiliza:

**`academico.ifsul.edu.br`**

O domínio é configurável por `ALLOWED_EMAIL_DOMAINS`.

A possibilidade de aceitar e-mails institucionais de outras instituições fica para uma eventual expansão futura.

## Cadastro durante o período de teste

Durante o piloto fechado, o cadastro também pode ser limitado por uma lista de e-mails convidados.

Quando `ALLOWED_EMAILS` possui registros:

- somente os e-mails presentes nessa lista podem criar uma nova conta;
- o e-mail também precisa pertencer ao domínio institucional permitido;
- usuários já cadastrados continuam podendo realizar Login normalmente.

Quando a lista estiver vazia, permanece apenas a restrição de domínio institucional.

## Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

Essa funcionalidade poderá ser avaliada futuramente.

## Recuperação de senha

A recuperação de senha não faz parte da versão atual.

Caso seja implementada futuramente, deverá utilizar um fluxo seguro com envio de link por e-mail e redefinição de senha.

## Regras adotadas

- **Tentativas:** 5 por minuto por combinação e-mail + IP no Login e 5 por minuto por IP no Cadastro. Não existe bloqueio permanente da conta por tentativas.
- **Sessão:** armazenada no PostgreSQL, válida por 7 dias sem uso e renovada a cada acesso. Não existe "Lembrar de mim".
- **Alteração de senha:** encerra as demais sessões da conta e mantém ativa a sessão atual.
- **Papéis:** `ALUNO`, `MODERADOR` e `ADMINISTRADOR`. Toda conta criada pelo Cadastro nasce como ALUNO.
- **Aceite dos Termos:** versão, data/hora e usuário são registrados na criação da conta.
- **Mudança de papel:** realizada normalmente pelo painel `/admin`. O workflow de promoção permanece como alternativa de emergência e para criação do primeiro ADMINISTRADOR.
---

# 5. Layout global

Componentes compartilhados das telas autenticadas:

- [x] Sidebar.
- [x] Header.
- [x] Avatar com iniciais.
- [x] Nome de exibição.
- [x] Sino de notificações.
- [x] Badge de notificações.
- [x] Menu mobile em formato de drawer.
- [x] Estados de loading.
- [x] Estados de erro.
- [x] componentes compartilhados.

## ALUNO

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil
- Configurações
- Sair

## MODERADOR

Possui as funcionalidades comuns do usuário e também:

- Moderação

## ADMINISTRADOR

Possui as funcionalidades comuns do usuário e também:

- Moderação
- Painel administrativo
---

# 6. Home

Implementado:

- [x] saudação ao usuário;
- [x] apresentação do ConectaCC;
- [x] consulta do estado do Perfil;
- [x] card "Complete seu perfil" enquanto o Perfil estiver incompleto;
- [x] desaparecimento do card após completar o Perfil;
- [x] atalhos para Estudos, Oportunidade, Comunidade e Perfil;
- [x] seção "Acessos institucionais";
- [x] adaptação do destaque conforme o Perfil esteja completo ou incompleto;
- [x] loading e erro parcial.

A Home utiliza os atalhos principais do ConectaCC e concentra também os acessos institucionais definidos para o MVP.

---

# 7. Perfil

Implementado:

- [x] visualização do Perfil;
- [x] edição;
- [x] Nome de exibição;
- [x] curso;
- [x] disciplinas atuais;
- [x] Sobre mim;
- [x] Interesses;
- [x] Posso ajudar com;
- [x] Procuro ajuda com;
- [x] GitHub;
- [x] LinkedIn;
- [x] avatar por iniciais;
- [x] validações;
- [x] confirmação de alterações não salvas;
- [x] Perfil público para usuários autenticados.

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

O campo Semestre não faz parte da versão atual.

O catálogo utiliza as 49 disciplinas obrigatórias da Matriz 2023.

---

# 8. Estudos

Implementado:

- [x] Minhas disciplinas;
- [x] Todas as disciplinas;
- [x] busca de disciplinas;
- [x] identificação de "Minha disciplina";
- [x] página da disciplina;
- [x] acesso à ementa oficial quando houver URL confirmada;
- [x] área de materiais em todas as disciplinas;
- [x] estado sem materiais;
- [x] formulário Compartilhar material;
- [x] envio de material por link;
- [x] materiais aprovados na página da disciplina;
- [x] integração dos materiais com Moderação;
- [x] Perfil → Meus conteúdos;
- [x] cancelamento de envio pendente;
- [x] edição moderada de material aprovado;
- [x] exclusão de material aprovado;
- [x] notificações relacionadas aos materiais;
- [x] recursos acadêmicos;
- [x] layout atualizado dos cards de disciplinas.

A Moderação utiliza uma fila única para Materiais e Oportunidades.

## Referência acadêmica

A referência utilizada é a:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Fontes institucionais:

### Grade Curricular

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

### Matriz / Organograma de pré-requisitos

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

A necessidade de contemplar estudantes vinculados à Matriz 2017 será verificada posteriormente.

No MVP não existe upload direto de arquivos.

---

# 9. Materiais

Os materiais são compartilhados por **link** e passam por Moderação antes de ficarem disponíveis para outros estudantes.

## Campos principais

- Título;
- Descrição opcional;
- Link;
- Disciplina;
- Autor;
- opção de compartilhar anonimamente;
- Status.

## Estados de Moderação

- PENDENTE;
- APROVADO;
- REJEITADO.

## Fluxo

Material enviado  
→ PENDENTE  
→ MODERADOR ou ADMINISTRADOR analisa  
→ APROVADO ou REJEITADO  
→ autor recebe notificação.

Quando aprovado:

→ o material aparece na disciplina correspondente em Estudos  
→ também pode aparecer no mural da Comunidade.

Quando rejeitado:

→ o material não é publicado  
→ o autor pode consultar o motivo da rejeição em Meus conteúdos.

## Autoria

Por padrão, o material aprovado mostra o **Nome de exibição** do autor.

O nome do autor identificado pode direcionar ao Perfil público.

No envio, o estudante pode escolher:

**Compartilhar anonimamente**

Nesse caso, o material aparece publicamente como:

**Compartilhado pela comunidade**

O sistema continua registrando internamente quem realizou o envio.

O MODERADOR não visualiza a identidade real do autor de um material anônimo.

O ADMINISTRADOR pode identificar o autor somente nas áreas administrativas autorizadas.

## Data pública

Quando aprovado, o material utiliza publicamente:

**a data de aprovação/publicação.**

A data original do envio permanece registrada internamente.
---

# 10. Oportunidade

Implementado:

- [x] acesso às oportunidades oficiais do IFSul;
- [x] oportunidades indicadas pela comunidade;
- [x] busca;
- [x] filtro por tipo;
- [x] cards;
- [x] formulário Adicionar oportunidade;
- [x] validações;
- [x] integração com Moderação;
- [x] loading;
- [x] erro;
- [x] estado vazio;
- [x] Meus conteúdos;
- [x] cancelamento de envio pendente;
- [x] exclusão;
- [x] notificações;
- [x] auditoria;
- [x] painel administrativo;
- [x] integração com o mural da Comunidade.

## Tipos

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

## Modalidades

- Remoto;
- Híbrido;
- Presencial.

As oportunidades indicadas pela comunidade só aparecem publicamente depois da aprovação.

O campo **Inscrições até** é opcional.

Quando não houver prazo informado, a oportunidade permanece visível por 15 dias após a aprovação.

Não fazem parte da versão atual:

- edição de oportunidade aprovada;
- envio anônimo.
---

# 11. Comunidade

A Comunidade faz parte da versão atual do ConectaCC.

Implementado:

- [x] mural com materiais e oportunidades aprovados;
- [x] ordenação dos conteúdos;
- [x] busca;
- [x] filtro por tipo;
- [x] visualização em lista e grade;
- [x] botão "Mostrar mais";
- [x] autoria identificada ou anônima para materiais;
- [x] acesso ao Perfil público do autor identificado;
- [x] acesso ao conteúdo correspondente;
- [x] opção "Compartilhar conteúdo";
- [x] compartilhamento de Material;
- [x] compartilhamento de Oportunidade;
- [x] card "Contribua com a comunidade";
- [x] seção "A comunidade em números".

Continuam para evoluções futuras:

- busca de estudantes;
- filtros de estudantes;
- busca de estudantes por disciplinas atuais;
- canais da comunidade;
- publicação externa como tipo próprio de conteúdo.

---

# 12. Perfil público

Implementar:

- [x] avatar;
- [x] Nome de exibição;
- [x] Curso;
- [x] Sobre;
- [x] Interesses;
- [x] Pode ajudar com;
- [x] Procura ajuda com;
- [x] Disciplinas atuais;
- [x] GitHub;
- [x] LinkedIn;
- [x] estados de loading;
- [x] erro;
- [x] Perfil indisponível.

Não exibir:

- e-mail institucional;
- Nome completo da conta quando diferente do Nome de exibição;
- dados de autenticação;
- dados administrativos.

---

# 13. Configurações

Implementar:

- [x] exibição do e-mail institucional em modo somente leitura;
- [x] Senha atual;
- [x] Nova senha;
- [x] Confirmar nova senha;
- [x] alteração de senha;
- [x] validações;
- [x] loading;
- [x] sucesso;
- [x] erro;
- [x] alterações não salvas.

Exclusão de conta não pertence à interface atual do MVP.

As regras relacionadas à exclusão de conta e aos dados associados estão definidas em `decisoes.md` 64.

A autoexclusão pela interface continua fora da versão atual.

---

# 14. Moderação

A aplicação utiliza uma fila única para:

- MATERIAL;
- OPORTUNIDADE.

Funcionalidades:

- [x] listar conteúdos PENDENTES;
- [x] ordenar mais antigos primeiro;
- [x] visualizar detalhes;
- [x] apresentar a autoria conforme as regras de anonimato;
- [x] abrir o link enviado;
- [x] Aprovar;
- [x] Rejeitar;
- [x] exigir motivo na rejeição;
- [x] gerar notificação;
- [x] retirar conteúdo processado da fila;
- [x] tratar conteúdo já processado;
- [x] impedir MODERADOR de moderar o próprio conteúdo;
- [x] permitir ADMINISTRADOR moderar o próprio conteúdo;
- [x] proteger a área para MODERADOR e ADMINISTRADOR.

A fila atual não possui filtro por tipo.

A autorização deve existir também no backend, e não apenas na interface.

Continuam como evoluções futuras:

- retirada administrativa de conteúdo publicado;
- reversão administrativa de rejeição;
- denúncia ou solicitação de revisão.
---

# 15. Concorrência na Moderação

A implementação deve impedir que duas decisões sejam aplicadas ao mesmo conteúdo.

Exemplo:

ADMIN A aprova
→ status deixa de ser PENDENTE.

ADMIN B tenta rejeitar o mesmo conteúdo
→ sistema identifica que ele já foi processado
→ segunda decisão não sobrescreve a primeira.

---

# 16. Notificações

As notificações informam o usuário sobre o resultado da análise dos conteúdos enviados.

## Eventos atuais

- [x] Material aprovado.
- [x] Material rejeitado.
- [x] Alteração de material aprovada.
- [x] Alteração de material rejeitada.
- [x] Oportunidade aprovada.
- [x] Oportunidade rejeitada.

Publicações como tipo próprio de conteúdo continuam fora da versão atual.

## Funcionalidades

- [x] badge numérico de não lidas;
- [x] limite visual `9+`;
- [x] indicação de notificações não lidas;
- [x] marcar como lida;
- [x] marcar todas como lidas;
- [x] estado vazio;
- [x] direcionamento ao conteúdo correspondente;
- [x] atualização periódica das notificações.

Ao clicar em uma notificação:

→ ela é marcada como lida  
→ o usuário é direcionado ao destino correspondente.

Exemplos:

Material aprovado  
→ disciplina correspondente em Estudos.

Material rejeitado  
→ item correspondente em Meus conteúdos.

Oportunidade aprovada  
→ oportunidade correspondente.

Oportunidade rejeitada  
→ item correspondente em Meus conteúdos.

## Motivo da rejeição

O motivo da rejeição não precisa aparecer diretamente na notificação.

O autor pode consultá-lo em:

**Meus conteúdos → detalhes do envio rejeitado.**
---

# 17. URLs e segurança

A aplicação valida links também no servidor.

Regras adotadas:

- somente HTTP e HTTPS;
- normalização segura quando aplicável;
- GitHub deve utilizar domínio correspondente;
- LinkedIn deve utilizar domínio correspondente;
- links externos devem utilizar práticas seguras.

Não confiar apenas na validação do frontend.

---

# 18. Limites de dados

## Perfil

Os limites atuais do Perfil ainda são considerados técnicos/provisórios:

- Nome de exibição: 120 caracteres;
- Sobre mim: 5.000 caracteres;
- cada tag: 255 caracteres;
- até 50 tags por campo.

## Material

- Título: 200 caracteres;
- Descrição: 2.000 caracteres;
- Link: 2.048 caracteres;
- Motivo da rejeição: 500 caracteres.

## Oportunidade

- Título: 200 caracteres;
- Descrição: 300 caracteres;
- Link: 2.048 caracteres;
- prazo de inscrição: opcional.

Publicação como tipo próprio de conteúdo continua fora da versão atual e seus limites ainda não precisam ser definidos.

---

# 19. Acessibilidade

Durante a implementação, garantir:

- [ ] labels reais;
- [ ] foco visível;
- [ ] navegação por teclado;
- [ ] foco preso em modais quando necessário;
- [ ] retorno do foco após fechamento;
- [ ] mensagens de erro associadas aos campos;
- [ ] feedback de loading;
- [ ] feedback de sucesso;
- [ ] estados que não dependem apenas de cor;
- [ ] links externos identificados;
- [ ] aviso ao fechar a página quando existirem alterações não salvas.

---

# 20. Responsividade

Implementar e testar:

- [ ] sidebar desktop;
- [ ] drawer mobile;
- [ ] cards responsivos;
- [ ] formulários em telas pequenas;
- [ ] modais responsivos;
- [ ] painel da Moderação responsivo;
- [ ] ações acessíveis no mobile.

---

# 21. Testes técnicos

Antes do piloto, realizar a validação completa de:

- [ ] Cadastro;
- [ ] Login;
- [ ] autorização;
- [ ] Perfil;
- [ ] Estudos;
- [ ] materiais;
- [ ] Oportunidade;
- [ ] Comunidade;
- [ ] Perfil público;
- [ ] Configurações;
- [ ] Moderação;
- [ ] Notificações;
- [ ] painel administrativo;
- [ ] auditoria;
- [ ] erros;
- [ ] concorrência;
- [ ] responsividade;
- [ ] acessibilidade.

A recuperação de senha deverá entrar nessa validação apenas caso seja implementada futuramente.

---

# 22. Estado das etapas de implementação

Principais etapas concluídas:

1. arquitetura e fundação;
2. autenticação;
3. layout global;
4. Perfil;
5. Home;
6. Estudos;
7. Materiais;
8. Meus conteúdos;
9. Moderação;
10. Notificações;
11. Perfil público;
12. Configurações;
13. painel administrativo;
14. auditoria;
15. Oportunidade;
16. Comunidade.

## Próximas etapas

Antes do piloto:

- revisar estados de erro e loading;
- realizar validação de responsividade;
- realizar validação de acessibilidade;
- executar os testes finais;
- corrigir problemas identificados;
- revisar os Termos de participação;
- preparar o ambiente do piloto;
- confirmar os participantes convidados;
- realizar a publicação da versão de teste.

---

# 23. Preparação e execução do piloto

Com a versão funcional do MVP, as próximas etapas são:

1. realizar testes internos;
2. corrigir problemas encontrados;
3. revisar os Termos de participação;
4. preparar o ambiente do piloto;
5. confirmar os participantes convidados;
6. publicar a versão de teste;
7. realizar testes com estudantes;
8. coletar feedback;
9. analisar os resultados;
10. priorizar melhorias;
11. planejar a próxima versão do ConectaCC.

## Possíveis evoluções posteriores

Podem ser avaliadas futuramente:

- recuperação de senha;
- confirmação de propriedade do e-mail institucional por link ou código;
- suporte a outras instituições e outros domínios de e-mail;
- busca de estudantes;
- canais da comunidade;
- novas formas de publicação e interação;
- demais funcionalidades registradas no roadmap.
---

# 24. Painel `/admin`

Entregue em 05/11/2026 (`decisoes.md` 65). É só para ADMINISTRADOR, e o papel é conferido a cada requisição.

## Usuários

- [x] lista com nome, nome de exibição, e-mail, papel e, a partir de 1280 px, data de cadastro;
- [x] busca por nome, nome de exibição e e-mail;
- [x] filtro por papel;
- [x] alteração de papel de outras contas, com confirmação;
- [x] recusa de mudança do próprio papel, na tela e na regra;
- [x] linha de log só com IDs internos e papéis.

O painel não cria, edita nem exclui contas.

## Moderações

- [x] lista de materiais em todas as situações, inclusive excluídos e alterações;
- [x] autor com a marca "Enviado anonimamente", quando for o caso;
- [x] quem decidiu e quando;
- [x] busca por título e autor;
- [x] filtro por situação;
- [x] detalhe com descrição, link, motivo da rejeição e dados originais da alteração.

O painel não aprova, rejeita, retira nem reverte conteúdo.

## Oportunidades

- [x] lista de oportunidades cadastradas;
- [x] consulta das informações da oportunidade;
- [x] visualização da autoria;
- [x] consulta da situação;
- [x] acesso às informações utilizadas para acompanhamento administrativo.

A área administrativa é de consulta e acompanhamento. A aprovação e rejeição continuam sendo realizadas pela Moderação do ConectaCC.

## Acesso

- [x] login próprio em `/admin/login`, com a mesma sessão do app;
- [x] página 403 em português para os demais papéis;
- [x] link "Painel administrativo" na sidebar, só para ADMINISTRADOR;
- [x] "Voltar ao ConectaCC" no rodapé da sidebar do painel;
- [x] tabelas sem rolagem lateral de 320 a 1920 px, empilhadas no celular;
- [x] layout igual ao do app (`backend/public/css/conectacc-admin.css`);
- [x] avatar desenhado localmente, sem serviço externo.
- [x] datas no horário de Brasília.

## Auditoria

Entregue em 07/11/2026 (`decisoes.md` 66).

- [x] tabela `audit_events`, protegida contra alteração e exclusão por trigger;
- [x] um evento por ação que muda dados, gravado na mesma transação;
- [x] versão anterior guardada na aprovação de uma alteração;
- [x] tela Auditoria, só de leitura, com filtros por ação, pessoa e período;
- [x] autor real do envio anônimo, com a marca, só para ADMINISTRADOR;
- [x] nenhum IP, e-mail, senha, hash, token ou motivo de rejeição nos eventos.
