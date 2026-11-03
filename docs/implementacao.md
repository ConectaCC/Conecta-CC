# Plano de Implementação — ConectaCC

Este documento organiza as principais etapas necessárias para transformar o MVP prototipado do ConectaCC em uma aplicação funcional.

O protótipo já define o comportamento esperado das principais telas e fluxos.

A implementação técnica será realizada posteriormente, após a definição da arquitetura.

---

# 1. Situação atual

Atualmente o ConectaCC possui:

- escopo do MVP definido;
- identidade visual definida;
- fluxos principais definidos;
- dez áreas principais prototipadas;
- comportamento ALUNO e ADMIN definido;
- estados de erro, loading e vazio previstos;
- princípios de responsividade e acessibilidade considerados;
- auditoria geral do protótipo realizada.
- documentação principal do MVP concluída;
- produto, UX e escopo do MVP fechados para o checkpoint com a professora.

As funcionalidades prototipadas ainda não devem ser consideradas implementadas.

Antes do início da arquitetura e da implementação, o projeto será analisado para confirmação da arquitetura idealizada. Caso sejam solicitadas alterações relevantes de produto, elas deverão ser avaliadas e incorporadas antes da criação da estrutura técnica definitiva.

---

# 2. Antes de escrever código

A equipe precisa definir a arquitetura técnica antes de iniciar o desenvolvimento funcional.

Principais decisões:

- stack;
- arquitetura geral;
- frontend;
- backend;
- banco de dados;
- autenticação;
- recuperação de senha;
- provedor de e-mail;
- autorização ALUNO/ADMIN;
- hospedagem;
- deploy;
- estratégia de testes;
- organização definitiva do código.

A etapa técnica deverá responder principalmente:

> **Como vamos construir o produto que já foi definido?**

Requisitos de produto não devem ser alterados silenciosamente por decisões técnicas.

Caso exista conflito entre uma decisão de arquitetura e os requisitos documentados, o conflito deverá ser apresentado à equipe antes da alteração do produto.

Essa etapa será conduzida principalmente por Marcelo Henrique Germiniani Panho, com apoio da equipe.

---

# 3. Fundação do projeto

- [x] Definir stack.
- [x] Definir arquitetura.
- [x] Definir estrutura de pastas do código.
- [x] Configurar ambiente de desenvolvimento.
- [x] Configurar banco de dados.
- [x] Configurar autenticação.
- [x] Configurar papéis ALUNO, MODERADOR e ADMINISTRADOR.
- [ ] Implementar autorização no backend (por enquanto, só a Gate de Moderação).
- [x] Configurar variáveis de ambiente.
- [x] Definir estratégia de branches e commits.
- [x] Definir ambiente de hospedagem.
- [x] Definir estratégia de deploy.

Detalhes da fundação: `docs/specs/2026-10-12-fundacao-design.md` e `docs/operacao.md`.

---

# 4. Autenticação

Implementar:

- [x] Login.
- [x] Cadastro.
- [x] validação do formato do e-mail.
- [x] validação do domínio institucional do IFSul.
- [x] Termos de participação versionados.
- [x] registro do aceite dos Termos somente após criação da conta.
- [x] Logout.
- [ ] Recuperação de senha (opcional — decisoes.md 52.9).
- [ ] envio de link de recuperação por e-mail (opcional — decisoes.md 52.9).
- [ ] validação do link de recuperação (opcional — decisoes.md 52.9).
- [x] proteção de rotas autenticadas.

## E-mail institucional

No MVP e no piloto:

**somente e-mail institucional do IFSul poderá ser utilizado no cadastro.**

Durante a implementação deverá ser confirmado tecnicamente qual domínio ou quais domínios do IFSul serão aceitos.

A possibilidade de aceitar e-mails de outras instituições pertence somente a uma eventual expansão futura do ConectaCC.

## Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

Essa funcionalidade não deve ser implementada nesta versão.

Ela poderá ser avaliada em uma evolução futura.

Essa decisão não altera a recuperação de senha, que continuará utilizando envio de e-mail.

## Decisões técnicas de autenticação ainda necessárias

- [x] definir limite ou bloqueio após várias tentativas de Login;
- [x] definir política de sessões;
- [ ] definir comportamento das outras sessões após alteração de senha;
- [ ] definir comportamento das outras sessões após recuperação de senha;
- [ ] definir formato, segurança e validade do link de recuperação;
- [ ] definir provedor responsável pelo envio dos e-mails.

## Regras adotadas (12/10/2026)

- **Tentativas:** 5 por minuto por combinação e-mail + IP no Login e 5 por minuto por IP no Cadastro. Ao estourar, a resposta é "Muitas tentativas. Tente novamente em X segundos.". Não há bloqueio de conta, para evitar que alguém tranque a conta de um colega de propósito.
- **Sessão:** guardada no PostgreSQL, válida por 7 dias sem uso e renovada a cada acesso; não expira ao fechar o navegador. Não há "Lembrar de mim".
- **Papéis:** enum `Role` (`ALUNO`, `MODERADOR`, `ADMINISTRADOR`) com `CHECK` no banco; a Gate `access-moderation` libera MODERADOR e ADMINISTRADOR. Toda conta criada pelo Cadastro nasce ALUNO.
- **Aceite dos Termos:** gravado na mesma transação que cria a conta (versão + data/hora + usuário). O texto vigente vem do backend (`backend/resources/terms/<versão>.json`).
- **Domínio aceito:** `academico.ifsul.edu.br`, configurável por `ALLOWED_EMAIL_DOMAINS`. A comparação é exata: subdomínios e sufixos são recusados.
- **Mudança de papel:** pelo comando `conectacc:promote`, executado em produção pelo workflow "Promover usuário" do GitHub Actions (o Render gratuito não tem Shell).

---

# 5. Layout global

Implementar os componentes compartilhados das telas autenticadas:

- [x] Sidebar.
- [x] Header.
- [x] Avatar com iniciais.
- [x] Nome de exibição.
- [ ] Sino de notificações (por enquanto, só o ícone).
- [ ] Badge de notificações.
- [x] Menu mobile em formato de drawer.
- [x] Estados de loading.
- [x] Estados de erro.
- [ ] Componentes compartilhados.

A navegação deve diferenciar corretamente:

## ALUNO

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil
- Configurações
- Sair

## ADMIN

Possui os mesmos itens e adicionalmente:

- Moderação

---

# 6. Home

Implementar:

- [x] saudação;
- [x] apresentação do ConectaCC;
- [x] atalhos principais;
- [x] consulta do estado do Perfil;
- [x] card "Complete seu perfil";
- [x] desaparecimento do card quando o Perfil estiver completo;
- [ ] integração com notificações;
- [x] loading e erro parcial.

---

# 7. Perfil

Implementar:

- [x] visualização do Perfil;
- [x] edição;
- [x] Nome de exibição;
- [x] curso;
- [ ] semestre (fora desta entrega — decisoes.md 53.3);
- [x] disciplinas atuais;
- [x] Sobre mim;
- [x] Interesses;
- [x] Posso ajudar com;
- [x] Procuro ajuda com;
- [x] GitHub;
- [x] LinkedIn;
- [x] avatar por iniciais;
- [x] validações;
- [x] confirmação de alterações não salvas.

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

Também deverá ser implementada a visibilidade adequada das informações para outros estudantes autenticados (fica para o Perfil público).

Implementado em 19/10/2026 (spec `specs/2026-10-19-perfil-design.md`). Catálogo com as 49 disciplinas obrigatórias da Matriz 2023 (decisoes.md 53.2).

---

# 8. Estudos

Estudos é implementado em duas etapas (decidido em 31/10/2026). O envio de material só entra na `main` junto com a Moderação, para nunca existir envio que ninguém consegue aprovar.

## Etapa 1 — Estudos (leitura)

- [x] Minhas disciplinas.
- [x] Todas as disciplinas.
- [x] busca de disciplinas.
- [x] identificação de "Minha disciplina".
- [x] página da disciplina.
- [x] acesso à ementa oficial quando houver URL confirmada.
- [x] estado sem materiais.

Todas as disciplinas têm área de materiais (decisão de 31/10/2026), então o estado "disciplina ainda sem área de materiais" (RF-34) não entra agora.

## Etapa 2 — Materiais + Moderação de material

Dividida em 2a (materiais e Moderação, entregue), 2b (Meus conteúdos, Editar, Excluir e Cancelar, entregue) e 2c (Notificações). Ver `decisoes.md` 55.1 e 55.6.


- [x] formulário Compartilhar material.
- [x] envio de material por link.
- [x] fila de Moderação só com MATERIAL (ver §14; PUBLICAÇÃO e OPORTUNIDADE entram com seus módulos).
- [x] materiais aprovados na página da disciplina.
- [x] Perfil → Meus conteúdos (filtros, "Mostrar mais", motivo da rejeição).
- [x] cancelar envio pendente.
- [x] editar material aprovado (alteração moderada, versão publicada mantida até a aprovação).
- [x] excluir material aprovado (soft delete).
- [x] notificações de material e alteração (etapa 2c).

## Referência acadêmica inicial

A implementação utilizará inicialmente como referência:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Fontes institucionais identificadas:

Grade Curricular:

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

Matriz / Organograma de pré-requisitos:

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

A necessidade de contemplar estudantes vinculados à Matriz 2017 será verificada posteriormente.

Esse levantamento não bloqueia a definição da arquitetura.

No MVP não existe upload direto de arquivos.

---

# 9. Materiais

Campos principais:

- Título;
- Descrição opcional;
- Link;
- Disciplina;
- Autor;
- Status.

Estados:

- PENDENTE;
- APROVADO;
- REJEITADO.

Fluxo:

Material enviado
→ PENDENTE
→ ADMIN analisa
→ APROVADO ou REJEITADO
→ notificação ao autor.

Para outros estudantes, o material pode aparecer como:

**Compartilhado pela comunidade**

O ADMIN continua identificando o autor.

No MVP atual, a autoria pública permanecerá como:

**Compartilhado pela comunidade**

A decisão sobre apresentar a autoria aos demais estudantes será retomada apenas no próximo semestre, após os primeiros testes.

## Data pública

Quando aprovado, o material deverá utilizar publicamente:

**a data de aprovação/publicação.**

A data original do envio poderá permanecer armazenada internamente.
---

# 10. Oportunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

Implementar:

- [ ] oportunidades institucionais;
- [ ] oportunidades da comunidade;
- [ ] busca;
- [ ] filtro por tipo;
- [ ] cards;
- [ ] formulário Indicar oportunidade;
- [ ] validações;
- [ ] integração com Moderação;
- [ ] loading;
- [ ] erro parcial;
- [ ] estado vazio.

Tipos iniciais:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

As oportunidades comunitárias só aparecem depois da aprovação.

Quando aprovadas, sua data pública deverá ser:

**a data de aprovação/publicação.**

A data original do envio permanece como informação interna.
---

# 11. Comunidade

> **Fora do MVP (02/11/2026):** decisão do Marcelo. Esta área fica para a próxima versão (`decisoes.md` 56). O texto abaixo é mantido como referência.

Implementar:

- [ ] feed cronológico;
- [ ] novos materiais no feed;
- [ ] publicações externas;
- [ ] Compartilhar com a comunidade;
- [ ] integração com Moderação;
- [ ] busca de estudantes;
- [ ] filtros;
- [ ] ordem alfabética;
- [ ] exclusão do próprio usuário dos resultados;
- [ ] busca por disciplinas atuais;
- [ ] botão "Mostrar mais";
- [ ] canais da comunidade.

Para publicações enviadas pela comunidade e aprovadas pela Moderação:

**a data pública será a data de aprovação/publicação.**

A data original do envio poderá permanecer registrada internamente.

A busca deve considerar:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

---

# 12. Perfil público

Implementar:

- [ ] avatar;
- [ ] Nome de exibição;
- [ ] Curso;
- [ ] Semestre;
- [ ] Sobre;
- [ ] Interesses;
- [ ] Pode ajudar com;
- [ ] Procura ajuda com;
- [ ] Disciplinas atuais;
- [ ] GitHub;
- [ ] LinkedIn;
- [ ] estados de loading;
- [ ] erro;
- [ ] Perfil indisponível.

Não exibir:

- e-mail institucional;
- Nome completo da conta quando diferente do Nome de exibição;
- dados de autenticação;
- dados administrativos.

---

# 13. Configurações

Implementar:

- [ ] exibição do e-mail institucional em modo somente leitura;
- [ ] Senha atual;
- [ ] Nova senha;
- [ ] Confirmar nova senha;
- [ ] alteração de senha;
- [ ] validações;
- [ ] loading;
- [ ] sucesso;
- [ ] erro;
- [ ] alterações não salvas.

Exclusão de conta não pertence à interface atual do MVP.

Sua política deverá ser definida antes do piloto.

---

# 14. Moderação

Implementar fila única para:

- MATERIAL;
- PUBLICAÇÃO;
- OPORTUNIDADE.

Funcionalidades:

- [x] listar conteúdos PENDENTES;
- [x] ordenar mais antigos primeiro;
- [ ] filtrar por tipo (volta quando houver um segundo tipo);
- [x] visualizar detalhes;
- [x] identificar autor;
- [x] abrir link enviado;
- [x] Aprovar;
- [x] Rejeitar;
- [x] informar motivo (obrigatório — decisoes.md 55.4);
- [ ] gerar notificação;
- [x] retirar conteúdo processado da fila;
- [x] tratar conteúdo já processado;
- [x] impedir MODERADOR de moderar o próprio conteúdo (ADMIN pode — decisoes.md 51.2);
- [x] proteger a área para MODERADOR e ADMIN.

A autorização deve existir no backend e no banco, não apenas na interface.

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

Implementar notificações para:

- [x] Material aprovado.
- [x] Material rejeitado.
- [ ] Publicação aprovada.
- [ ] Publicação rejeitada.
- [ ] Oportunidade aprovada.
- [ ] Oportunidade rejeitada.

Também implementar:

- [x] badge numérico;
- [x] limite visual 9+;
- [x] não lidas;
- [x] marcar como lida;
- [x] marcar todas como lidas;
- [x] estado vazio;
- [ ] motivo da rejeição quando houver.

> **Atualização (02/11/2026):** o clique marca como lida e leva ao conteúdo (`decisoes.md` 55.5). O motivo da rejeição fica em Meus conteúdos, para onde a notificação de rejeição leva.

Ainda deverá ser definido o comportamento ao clicar em uma notificação.

Essa decisão deverá ser tomada antes da implementação definitiva deste módulo.

Ela não bloqueia:

- o checkpoint com a professora;
- a definição da arquitetura geral;
- a implementação dos módulos anteriores.
---

# 17. URLs e segurança

A implementação deverá validar links também no servidor.

Regras previstas:

- somente HTTP e HTTPS;
- normalização segura quando aplicável;
- GitHub deve utilizar domínio correspondente;
- LinkedIn deve utilizar domínio correspondente;
- links externos devem utilizar práticas seguras.

Não confiar apenas na validação do frontend.

---

# 18. Limites de dados

Antes ou durante a implementação deverão ser definidos limites para:

- Nome de exibição;
- Sobre mim;
- quantidade de tags;
- tamanho das tags;
- título de material;
- descrição de material;
- título de oportunidade;
- descrição de oportunidade;
- título de publicação;
- descrição de publicação;
- motivo da rejeição.

Esses valores ainda não estão definidos.

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

Antes do piloto, realizar testes de:

- [ ] Cadastro;
- [ ] Login;
- [ ] recuperação de senha;
- [ ] autorização;
- [ ] Perfil;
- [ ] Estudos;
- [ ] materiais;
- [ ] Oportunidade;
- [ ] Comunidade;
- [ ] Perfil público;
- [ ] Configurações;
- [ ] Moderação;
- [ ] notificações;
- [ ] erros;
- [ ] concorrência;
- [ ] responsividade;
- [ ] acessibilidade.

---

# 22. Ordem sugerida de implementação

Uma ordem possível é:

1. arquitetura e fundação;
2. autenticação;
3. layout global;
4. Perfil;
5. Home;
6. Estudos — etapa 1 (leitura);
7. Estudos parte 2: 2a Materiais + núcleo de Moderação (só MATERIAL), 2b Meus conteúdos e 2c Notificações;
8. ~~Oportunidade~~ e ~~Comunidade~~: fora do MVP (`decisoes.md` 56);
9. Perfil público;
10. painel `/admin` (Filament) para o ADMINISTRADOR;
11. notificações;
12. Configurações;
13. estados de erro e loading;
14. responsividade;
15. acessibilidade;
16. testes;
17. deploy;
18. preparação do piloto.

A ordem poderá ser ajustada após a definição da arquitetura.

---

# 23. Depois da implementação

Com uma versão funcional será possível:

1. realizar testes internos;
2. corrigir problemas;
3. preparar o piloto;
4. selecionar disciplinas e participantes;
5. revisar os Termos;
6. realizar testes com estudantes;
7. coletar feedback;
8. analisar os resultados;

## Possíveis evoluções posteriores

Não fazem parte da implementação atual, mas poderão ser avaliadas futuramente:

- confirmação de propriedade do e-mail institucional por link ou código;
- suporte a outras instituições e outros domínios de e-mail;
- revisão da autoria pública dos materiais após testes com estudantes.
10. priorizar melhorias;
11. planejar a próxima versão do ConectaCC.
