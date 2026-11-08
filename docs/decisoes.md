# Registro de Decisões — ConectaCC

Este documento registra decisões importantes tomadas durante a definição do ConectaCC.

O objetivo é evitar que decisões já discutidas sejam perdidas ou reabertas sem necessidade durante a implementação.

Este documento registra principalmente decisões de:

- produto;
- experiência do usuário;
- escopo;
- regras funcionais;
- prototipação.

Decisões de arquitetura técnica serão adicionadas posteriormente, depois da análise conduzida por Marcelo Henrique Germiniani Panho.

---

# 1. Princípio central do projeto

## Decisão

O ConectaCC deve:

> **conectar o que já existe e criar novas formas de colaboração entre estudantes.**

## Consequência

O ConectaCC não deve tentar substituir:

- Moodle;
- SUAP;
- páginas institucionais;
- sites do IFSul;
- sistemas acadêmicos existentes.

Quando uma informação já possuir fonte oficial:

→ o ConectaCC deve preferencialmente direcionar para a fonte original.

---

# 2. Público inicial

## Decisão

O MVP será inicialmente voltado para:

**estudantes de Ciência da Computação do IFSul – Câmpus Passo Fundo.**

## Fora do MVP

A expansão para:

- outros cursos;
- outros câmpus;
- outras instituições;

fica para evolução futura.

---

# 3. Nome visual

## Decisão

O nome oficial utilizado na interface e documentação é:

**ConectaCC**

Evitar variações como:

- Conecta CC;
- Conecta-CC;
- conectaCC.

O nome técnico do repositório pode seguir convenções diferentes.

---

# 4. Navegação principal

## Decisão

A sidebar do ALUNO possui, nesta ordem:

1. Home
2. Estudos
3. Oportunidade
4. Comunidade
5. Perfil

Grupo inferior:

- Configurações
- Sair

Para ADMIN:

- Moderação

aparece acima de Configurações.

---

# 5. Nome da área Estudos

## Decisão

A área acadêmica será chamada:

**Estudos**

Não utilizar no menu:

- Acadêmico;
- CC Estudos.

---

# 6. Oportunidade no singular

## Decisão

O nome do item da navegação é:

**Oportunidade**

No conteúdo da página podem ser utilizadas expressões no plural, como:

**Oportunidades indicadas pela comunidade.**

---

# 7. Papéis do MVP

## Decisão

O MVP possui dois papéis:

- ALUNO;
- ADMIN.

ADMIN possui as funcionalidades comuns do usuário e adicionalmente acesso à Moderação.

> **Atualização (12/10/2026):** substituída. O MVP passa a ter três papéis: ALUNO, MODERADOR e ADMINISTRADOR. Ver `requisitos.md`, seção 1.

---

# 8. Perfil completo

## Decisão

Um Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

Todos os demais campos são opcionais.

## Consequência

Enquanto o Perfil estiver incompleto:

→ a Home apresenta **Complete seu perfil**.

Depois de completo:

→ o card desaparece.

Não utilizar porcentagem ou gamificação de completude.

---

# 9. Nome de exibição

## Decisão

O cadastro solicita:

**Nome completo**

Esse valor é utilizado inicialmente como:

**Nome de exibição**

Depois, o estudante pode alterar o Nome de exibição no Perfil.

O Nome completo não precisa ser solicitado novamente no Perfil.

---

# 10. Avatar

## Decisão

No MVP, o avatar utiliza iniciais.

Exemplos:

Viviane Biazus Aneris → VA

Marcelo Henrique Germiniani Panho → MP

## Pós-MVP

Upload de foto de Perfil poderá ser avaliado futuramente.

---

# 11. Curso

## Decisão

No MVP, o curso é:

**Ciência da Computação**

Não existe seletor de curso.

---

# 12. Disciplinas atuais

## Decisão

O Perfil poderá selecionar disciplinas atuais.

A lista deverá possuir todas as disciplinas confirmadas do curso, e não apenas as disciplinas piloto.

## Consequência

As disciplinas selecionadas alimentam:

**Minhas disciplinas**

em Estudos.

---

# 13. Visibilidade das disciplinas atuais

## Decisão

As disciplinas atuais podem ser visualizadas por outros estudantes autenticados no ConectaCC.

Elas não são públicas para a internet.

## Consequência

Antes do piloto real:

→ os Termos de participação deverão informar essa visibilidade explicitamente.

> **Atualização (12/10/2026):** reconfirmada. Ver seção 51.

---

# 14. Perfil público

## Decisão

Perfis podem ser vistos por outros usuários autenticados.

No MVP não existe:

- Perfil público para a internet;
- opção “Quero aparecer na Comunidade”;
- controle individual de visibilidade.

Essas possibilidades ficam para evolução futura.

---

# 15. Busca de estudantes

## Decisão

A busca da Comunidade poderá considerar:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

## Regras

- o próprio usuário não aparece nos próprios resultados;
- sem busca, utilizar ordem alfabética;
- disciplina atual não significa que o estudante pode ajudar naquela disciplina.

---

# 16. Linguagem de ajuda

## Decisão

No Perfil do próprio usuário:

- Posso ajudar com
- Procuro ajuda com

Nas visualizações públicas:

- Pode ajudar com
- Procura ajuda com

---

# 17. Materiais do MVP

## Decisão

Materiais serão compartilhados por:

**link**

Não haverá upload direto de arquivos no MVP.

## Motivo

Upload adicionaria complexidade relacionada a:

- armazenamento;
- tipos de arquivo;
- tamanho;
- segurança;
- direitos autorais;
- infraestrutura.

---

# 18. Autoria dos materiais

## Decisão atual do MVP

Publicamente, os materiais aparecem como:

**Compartilhado pela comunidade**

O ADMIN continua identificando o autor para:

- Moderação;
- notificação;
- controle interno do envio.

## Decisão futura

A decisão sobre exibir ou não a autoria dos materiais publicamente não será tomada neste semestre.

Ela será retomada:

**no próximo semestre, após os primeiros testes com estudantes.**

Até essa avaliação, permanece o comportamento atual:

**Compartilhado pela comunidade**

> **Atualização (12/10/2026):** substituída. Agora o padrão é mostrar o Nome de exibição, com a opção “Publicar anonimamente”. Ver seção 51.

---

# 19. Catálogo acadêmico e disciplinas piloto

## Fonte acadêmica inicial

A **Matriz 2023** do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo será utilizada como referência acadêmica inicial do MVP.

Fontes institucionais identificadas:

### Grade Curricular

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

### Matriz / Organograma de pré-requisitos

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 organiza o curso do:

**1º ao 8º semestre.**

As fontes institucionais permitem obter informações como:

- disciplinas;
- carga horária;
- ementas;
- organização por semestre;
- pré-requisitos.

## Disciplinas piloto

Todas as disciplinas confirmadas podem existir no Perfil e no catálogo de Estudos.

Somente algumas poderão inicialmente possuir:

- materiais;
- compartilhamento;
- área colaborativa.

Essas serão utilizadas como disciplinas piloto.

As disciplinas piloto ainda precisam ser escolhidas.

## Matriz 2017

A necessidade de contemplar estudantes ainda vinculados à Matriz 2017 será verificada posteriormente.

Esse levantamento não é necessário para as atividades atuais e não bloqueia a definição da arquitetura.

---

# 20. Links institucionais

## Decisão

Nunca inventar URL institucional.

Quando um endereço ainda não estiver confirmado:

→ não apresentar link falso.

No protótipo pode existir:

**Link oficial a confirmar**

Em produção, o link só aparece depois da confirmação da URL.

O levantamento dos links institucionais que ainda faltam será retomado após o novo mapeamento.

Esse levantamento é a próxima atividade de produto e não bloqueia a definição inicial da arquitetura.

---

# 21. Oportunidades

## Decisão

A área Oportunidade separa:

### Oportunidades do IFSul

Recursos institucionais curados.

### Oportunidades indicadas pela comunidade

Conteúdos enviados por estudantes e moderados.

Não misturar as duas origens em uma única lista no MVP.

---

# 22. Tipos de oportunidade

## Decisão

Os tipos iniciais são:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

Não permitir criação livre de novos tipos no MVP.

---

# 23. Data pública dos conteúdos colaborativos

## Decisão

Para conteúdos que passam por Moderação, a data apresentada publicamente será:

**a data de aprovação/publicação.**

A regra vale para:

- Material;
- Publicação;
- Oportunidade.

Exemplo:

Conteúdo enviado na segunda-feira  
→ permanece PENDENTE  
→ ADMIN aprova na quarta-feira  
→ publicamente aparece como publicado na quarta-feira.

A data original de envio permanece como informação interna.

## Motivo

Um conteúdo só passa a existir publicamente depois da aprovação.

Utilizar a data original do envio poderia fazer um conteúdo recém-publicado aparecer para os usuários como antigo.
---

# 24. Comunidade

## Decisão

A Comunidade possui três áreas:

1. O que está rolando
2. Encontre estudantes
3. Conecte-se com a comunidade

O módulo não deve se transformar em uma rede social completa no MVP.

---

# 25. Feed

## Decisão

O feed é:

- cronológico;
- simples;
- do mais recente para o mais antigo.

Não possui:

- algoritmo de recomendação;
- curtidas;
- comentários;
- seguidores;
- ranking;
- repost.

---

# 26. Compartilhamento na Comunidade

## Decisão

No MVP, publicações compartilhadas possuem:

- Título;
- Descrição;
- Link.

A publicação passa por Moderação antes de aparecer no feed.

Não existe postagem apenas de texto ou upload de imagem/arquivo nesta versão.

---

# 27. Canais da Comunidade

## Decisão

Foram previstos:

- grupo de WhatsApp dos estudantes;
- Instagram institucional;
- Arena Games.

Links ainda não confirmados não devem ser inventados.

---

# 28. Configurações

## Decisão

Configurações possui somente:

### Conta

- E-mail institucional somente leitura.

### Segurança

- alteração de senha.

Não adicionar opções apenas para preencher a página.

---

# 29. Exclusão de conta

## Decisão

Não existe exclusão automática de conta na interface atual do MVP.

## Obrigatório antes do piloto

Definir:

- procedimento de exclusão;
- exclusão de dados;
- responsabilidades;
- retenção;
- impacto sobre conteúdos enviados.

---

# 30. Senha

## Decisão

Regra mínima:

- 8 caracteres;
- confirmação igual.

Não exigir obrigatoriamente:

- símbolo;
- número;
- letra maiúscula.

---

# 31. Recuperação de senha

## Decisão

Não utilizar fluxo inseguro:

E-mail
→ Nova senha.

O fluxo correto é:

E-mail
→ envio de instruções
→ link recebido
→ Nova senha
→ sucesso.

A implementação de token e validade será decidida na arquitetura.

> **Atualização (12/10/2026):** a recuperação de senha deixou de ser obrigatória na primeira entrega e só será implementada se houver tempo. Se for, valem as regras acima. Ver seção 52.9.

---

# 32. E-mail institucional e confirmação de cadastro

## E-mail aceito no MVP e no piloto

O cadastro do MVP e do piloto será realizado utilizando:

**e-mail institucional do IFSul.**

A possibilidade de aceitar e-mails institucionais de outras instituições fica reservada para uma eventual expansão futura do ConectaCC para fora do IFSul.

A confirmação técnica do domínio ou dos domínios exatos utilizados pelo IFSul será realizada durante a implementação.

## Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

Essa funcionalidade poderá ser avaliada em uma evolução futura do ConectaCC.

## Importante

Continuam sendo questões diferentes:

- validação do formato do e-mail;
- validação do domínio institucional;
- confirmação da propriedade do endereço.

No MVP e no piloto serão aplicadas as validações necessárias de formato e domínio, mas não a confirmação de propriedade por link ou código.

Essa decisão não altera o fluxo de recuperação de senha, que continuará utilizando envio de e-mail.

> **Atualização (12/10/2026):** a recuperação de senha deixou de ser obrigatória na primeira entrega e só será implementada se houver tempo. Se for, valem as regras acima. Ver seção 52.9.
---

# 33. Notificações

## Decisão

No MVP, notificações utilizam:

- sino;
- contador;
- dropdown;
- marcar como lida;
- marcar todas como lidas.

Tipos principais:

- Material aprovado/rejeitado;
- Publicação aprovada/rejeitada;
- Oportunidade aprovada/rejeitada.

Não criar página específica de notificações no MVP.

## Decisão futura

O comportamento ao clicar em uma notificação ainda será definido.

Será necessário decidir, antes da implementação definitiva de Notificações:

- se clicar marca automaticamente como lida;
- se a notificação direciona ao conteúdo correspondente;
- qual será o destino para cada tipo de conteúdo.

Essa decisão não bloqueia a definição da arquitetura geral.

---

# 34. Moderação

## Decisão

Existe uma única fila de Moderação.

Tipos:

- MATERIAL;
- PUBLICAÇÃO;
- OPORTUNIDADE.

Estados:

- PENDENTE;
- APROVADO;
- REJEITADO.

Não criar três sistemas de Moderação separados.

---

# 35. Ordem da fila de Moderação

## Decisão

Pendências são ordenadas:

**mais antigas primeiro**

para priorizar quem está esperando há mais tempo.

---

# 36. ADMIN e próprio conteúdo

## Decisão

ADMIN não pode:

- Aprovar;
- Rejeitar;

conteúdo de sua própria autoria.

Seu conteúdo:

- aparece na fila;
- possui indicação “Seu envio”;
- pode ser consultado;
- deve ser analisado por outro ADMIN.

## Consequência para o piloto

Se ADMIN também utilizar a plataforma como estudante:

→ deverá existir mais de um ADMIN.

> **Atualização (12/10/2026):** substituída. A restrição vale só para MODERADOR, e ADMINISTRADOR pode moderar o próprio conteúdo. Ver seção 51.

---

# 37. Motivo da rejeição

## Decisão

O motivo da rejeição é:

**opcional**

Quando informado:

→ pode ser exibido ao autor na notificação.

Antes do piloto deverá existir orientação básica de redação para administradores.

> **Atualização (12/10/2026):** substituída. O motivo é obrigatório na rejeição feita por MODERADOR. Ele não precisa aparecer na notificação, mas o autor o vê em Meus conteúdos → Detalhes do envio rejeitado. Ver seção 52.2.

---

# 38. Concorrência na Moderação

## Decisão

Uma segunda decisão nunca deve sobrescrever uma decisão anterior.

Se um conteúdo já tiver sido analisado:

→ apresentar estado equivalente a:

**Este conteúdo já foi analisado.**

A implementação técnica será definida posteriormente.

---

# 39. Conteúdo aprovado

## Decisão

Quando aprovado:

Material  
→ Estudos

Publicação  
→ Comunidade

Oportunidade  
→ Oportunidade

O conteúdo sai da fila de pendentes e o autor recebe notificação.

---

# 40. Conteúdo rejeitado

## Decisão

Quando rejeitado:

- não é publicado;
- sai da fila;
- autor recebe notificação;
- motivo pode ser incluído.

---

# 41. “Mostrar mais”

## Decisão

No MVP, feed e lista de estudantes utilizam:

**Mostrar mais**

em vez de rolagem infinita.

A quantidade inicial ainda será definida durante a implementação.

---

# 42. Identidade visual

## Decisão

O azul é a base da identidade.

Referências principais:

- `#071F43`
- `#0D1933`
- `#2259B9`
- `#4D84CF`
- `#A3C5F2`
- `#EEF7FC`

Ver:

`identidade-visual.md`

---

# 43. Cores semânticas

## Decisão

Verde:

- sucesso;
- aprovação;
- operação concluída.

Vermelho:

- erro;
- rejeição;
- ação destrutiva.

Âmbar:

- pendência;
- atenção.

Disponibilidade de materiais utiliza azul, e não verde.

---

# 44. Responsividade

## Decisão de protótipo

Desktop:

- sidebar fixa.

Mobile:

- sidebar vira drawer.

A estratégia poderá ser refinada na implementação sem alterar a lógica de navegação.

---

# 45. Escopo social

## Decisão

Não pertencem ao MVP:

- chat;
- comentários;
- curtidas;
- seguidores;
- mensagens diretas;
- matching;
- Pedir ajuda.

Essas possibilidades ficam no roadmap.

---

# 46. Inteligência Artificial

## Decisão

IA não faz parte do MVP atual.

Possibilidades como:

- Agente Orienta;
- recomendações;
- apoio à navegação;

ficam para análise futura.

---

# 47. Arquitetura

## Status

Ainda não definida.

Decisões como:

- stack;
- frontend;
- backend;
- banco;
- hospedagem;
- autenticação;
- deploy;

serão analisadas posteriormente por Marcelo Henrique Germiniani Panho com apoio da equipe.

Não criar estrutura técnica definitiva no repositório antes dessa decisão.

---

# 48. Princípio para decisões futuras

Antes de incluir uma nova funcionalidade, avaliar:

1. Qual problema ela resolve?
2. Existe necessidade demonstrada?
3. Já existe solução institucional?
4. O ConectaCC precisa construir ou apenas conectar?
5. Quanto aumenta a complexidade?
6. É possível testar uma versão mais simples?
7. Existe capacidade de manutenção?

---

# 49. Estado do protótipo

As dez áreas principais do MVP foram:

- definidas;
- prototipadas;
- congeladas;
- auditadas.

A auditoria final considerou o protótipo consistente o suficiente para servir como referência para implementação.

Isso não significa que o sistema esteja implementado.

---

# 50. Manutenção deste documento

Novas decisões relevantes devem ser adicionadas aqui durante o projeto.

Quando uma decisão técnica for tomada, registrar:

- o que foi decidido;
- por que foi decidido;
- quais alternativas foram consideradas;
- consequências principais;
- data da decisão.

Isso permitirá manter histórico e reduzir retrabalho.

---

# 51. Rodada de atualização — 12/10/2026

Decisões tomadas ao resolver as questões abertas da atualização do protótipo.

## 51.1 Ações do autor sobre o próprio conteúdo

O autor gerencia o próprio conteúdo pelo menu **⋯** no card público, tanto no conteúdo identificado quanto no anônimo.

- Opções: **Editar** e **Excluir**.
- O menu só aparece para o autor. Outros alunos não o veem.
- No conteúdo anônimo, o card continua mostrando **Compartilhado pela Comunidade**.
- Para o MODERADOR, a existência dessas ações não pode revelar quem é o autor anônimo.

Com isso, anonimato passa a significar que a identidade do autor fica oculta para a comunidade e para o MODERADOR, mas o próprio autor reconhece e gerencia o seu conteúdo.

**Editar:** Editar → alterar dados → Enviar alteração para análise. Enquanto a alteração estiver pendente, a versão pública antiga continua visível. Ela só é substituída depois da aprovação.

**Excluir:** Excluir → modal de confirmação → confirmar. O conteúdo some das áreas públicas imediatamente (soft delete), sem depender da Moderação.

## 51.2 MODERADOR e próprio conteúdo

O conteúdo do próprio MODERADOR **não aparece na fila dele**. Ele é analisado por outro MODERADOR ou por um ADMINISTRADOR.

## 51.3 Versão dos Termos de participação

O texto dos Termos ganhou a regra de autoria e anonimato e **continua na versão 0.1**, porque nenhum usuário aceitou uma versão ainda.

## 51.4 E-mail já cadastrado

Fica a mensagem:

> Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.

Sem confirmação de e-mail no MVP, qualquer resposta diferente de “conta criada” já revela que o e-mail existe. A decisão foi só sobre o tom do texto. O trecho “ou recupere sua senha” saiu por causa da decisão 51.5.

## 51.5 Recuperação de senha fora do MVP inicial

> **Substituída pela seção 52.9:** a recuperação passou a ser opcional na primeira entrega, e não futura.

Registro original: todo o fluxo de recuperação ficaria para depois do MVP inicial, o link “Esqueci minha senha” sairia do Login e quem esquecesse a senha não teria tratamento, porque o MVP inicial será uma versão fechada para poucos alunos.

## 51.6 Visibilidade das disciplinas atuais

As disciplinas atuais ficam **visíveis para usuários autenticados**: no Perfil público e na busca da Comunidade. Isso reconfirma as seções 13 e 15.

## 51.7 Título da seção no Perfil

A seção que contém as Disciplinas atuais continua com o título **Minha jornada acadêmica**.

---

# 52. Decisões após o prompt mestre — 12/10/2026

Complementam a seção 51 e substituem regras antigas que entrem em conflito.

## 52.1 Perfil → Meus conteúdos entra no MVP

Área privada do próprio usuário para consultar e gerenciar os conteúdos e solicitações vinculados à sua conta, **inclusive os anônimos**. Não é um histórico administrativo completo.

- Uma única lista, da mais recente para a mais antiga, sem abas por status.
- Filtro por status: Todos, Em andamento, Aprovados, Rejeitados, Excluídos.
- “Em andamento” é só um agrupamento visual (por exemplo, PENDENTE). **Não** cria um status persistido.
- Cada item mostra: título, código da solicitação, tipo (Material/Publicação/Oportunidade), módulo (Estudos/Comunidade/Oportunidade), data do envio, status, indicação de anônimo quando aplicável e as ações permitidas.

## 52.2 Motivo da rejeição visível ao autor

Em Meus conteúdos → **Detalhes do envio rejeitado**, o autor vê o status REJEITADO e o motivo registrado pela Moderação. A notificação pode continuar mostrando só “Rejeitado”.

No MVP não há contestação, reavaliação nem recurso.

## 52.3 Ações do autor no card público

Confirma a seção 51.1. O menu ⋯ com Editar e Excluir aparece só para o autor, no conteúdo identificado e no anônimo. Isso não quebra o anonimato perante terceiros:

- outros ALUNOS e o MODERADOR não descobrem o autor;
- nas áreas públicas, o ADMINISTRADOR também vê “Comunidade”;
- só as áreas administrativas autorizadas revelam a autoria real ao ADMINISTRADOR.

Fica substituída a regra de que a interface pública não poderia mostrar nenhum indício de propriedade nem ao próprio autor.

## 52.4 Edição de conteúdo aprovado

Mantida: a versão aprovada continua pública enquanto a edição está pendente e só é substituída após aprovação. Se a edição for rejeitada, a versão anterior permanece. A edição preserva a condição de autoria (identificado ou anônimo).

## 52.5 Solicitação em andamento

Não pode ser modificada diretamente. O usuário pode **pedir o cancelamento**. Depois do cancelamento efetivo, qualquer novo envio ou edição gera uma nova solicitação.

**Em aberto:** se o cancelamento é imediato ou processado por alguém, como a solicitação cancelada é representada, se existe status CANCELADA e em qual filtro ela aparece.

## 52.6 Conteúdo excluído

Continua listado em Meus conteúdos, identificado como **Excluído**, e pode ser consultado. A exclusão pública segue com soft delete. Não há restauração pelo usuário no MVP.

## 52.7 Ações por situação em Meus conteúdos

| Situação | Ações no MVP |
|---|---|
| Aprovado | Ver conteúdo, Editar, Excluir |
| Em andamento / Pendente | Ver dados enviados, solicitar cancelamento (sem editar) |
| Rejeitado | Ver dados enviados, ver detalhes e motivo da rejeição |
| Excluído | Consultar; sem restauração |

## 52.8 Disciplinas atuais

Confirma a seção 51.6: visíveis para usuários autenticados, sem opção de ocultar no MVP. Os Termos devem informar isso antes do piloto. Uma opção futura de ocultar exige antes definir as consequências para busca e descoberta.

## 52.9 Recuperação de senha opcional na primeira entrega

Cadastro, Login, autenticação e sessão continuam obrigatórios. O fluxo Esqueci minha senha → e-mail → link → redefinir senha **só será implementado se houver tempo**. Se não for, fica para a próxima evolução. Se for implementado, valem as regras já definidas. Alterar a senha em Configurações continua no MVP.

## 52.10 Ações futuras em cards — não implementar

Salvar, Compartilhar, Copiar link, Denunciar, notificações por disciplina, módulo ou tipo, Ocultar, “Ver menos conteúdos como este”, Tenho interesse / Não tenho interesse. Nenhuma pertence ao MVP, e as regras delas não estão definidas.

> **Atualização (08/11/2026):** a regra principal de Denunciar foi decidida na §67.2. Ela continua pós-MVP.

## 52.11 Pendências que continuam

- Cancelamento: fluxo, se é imediato, quem processa, representação e filtro.
- Rejeitado: se poderá ser editado e reenviado direto de Meus conteúdos ou se exige um novo envio.
- Filtro por tipo em Meus conteúdos: avaliar se for simples. Busca textual fica para o futuro.
- Continuam futuros: histórico de versões, linha do tempo, comparação antes/depois, auditoria completa, mostrar ao usuário comum quem moderou, portfólio, métricas, reavaliação e restauração pelo usuário.

> **Atualização (04/11/2026):** o filtro por tipo e a busca textual em Meus conteúdos ficam fora do MVP (§60).

---

# 53. Perfil e catálogo de disciplinas — 19/10/2026

Decisões tomadas na definição da segunda entrega (spec `specs/2026-10-19-perfil-design.md`).

## 53.1 Referência, não cópia

O ConectaCC guarda a **referência oficial** de cada disciplina (o link do PDF da ementa no site do IFSul), e não uma cópia do PDF nem do conteúdo institucional. O site do IFSul continua sendo a fonte canônica. Quando a página da disciplina existir, ela mostra "Ver ementa oficial ↗".

O princípio vale para integrações futuras (Moodle, SUAP, páginas institucionais): o ConectaCC armazena referências, metadados e vínculos necessários para organizar e relacionar a informação, sem duplicar o que já existe em outro sistema.

## 53.2 Catálogo

- Entram as **49 disciplinas obrigatórias** da Matriz 2023, do 1º ao 8º semestre, com nome, semestre, carga horária e link da ementa.
- Os nomes seguem o site do IFSul. Única correção: "Gestão Empresarial e Empreendedorismo" (o site traz "Empreededorismo").
- As **eletivas** ficam fora por enquanto. A Matriz 2017 será verificada depois.
- Todas as disciplinas do catálogo podem ser escolhidas no Perfil, mesmo que só algumas tenham área de materiais. As disciplinas piloto serão escolhidas depois e não bloqueiam o catálogo.

## 53.3 Perfil nesta entrega

- O campo **Semestre** não entra ainda.
- As **sugestões** de tags ("Sugestões: + Git…") ficam para o futuro.
- Para salvar o Perfil são obrigatórios o Nome de exibição e pelo menos uma disciplina atual, como no protótipo.
- Os cards do Perfil ocupam toda a largura da área de conteúdo, e não só uma coluna à esquerda como no protótipo.
- GitHub e LinkedIn aceitam **só o endereço do perfil da pessoa**. Query string e fragmento são descartados, e o LinkedIn aceita subdomínio de país (`br.linkedin.com`). Os links são salvos como `https://github.com/usuario` e `https://www.linkedin.com/in/usuario`.
- Os limites de texto definitivos continuam pendentes (`pendencias.md` 2.7). Até lá vale um teto técnico provisório (Nome de exibição 120, Sobre mim 5.000, 255 por tag, 50 tags por campo), sem contador na tela.

---

# 54. Estudos (etapa 1) — 31/10/2026

Decisões tomadas na definição de Estudos (spec `specs/2026-10-31-estudos-design.md`).

## 54.1 Duas etapas

Estudos é implementado em duas etapas. A etapa 1 é só leitura: Minhas disciplinas, Todas as disciplinas, busca e página da disciplina. A etapa 2 traz Compartilhar material junto com a Moderação de material. As duas metades da etapa 2 entram juntas na `main`, para nunca existir um envio que ninguém consegue aprovar.

## 54.2 Materiais em todas as disciplinas

Todas as 49 disciplinas do catálogo têm área de materiais. Não existe lista de disciplinas piloto para materiais. Isso substitui, para materiais, o item "Disciplinas piloto" (§19) e a nota de 53.2. O estado "Materiais ainda não disponíveis" (RF-34) não aparece enquanto essa regra valer.

## 54.3 Tela Estudos e página da disciplina

- Todas as disciplinas aparecem agrupadas por semestre (1º a 8º), em ordem alfabética dentro de cada grupo. A busca ignora acentos e maiúsculas.
- Cada semestre abre e fecha ao clicar, e todos começam fechados. Durante a busca, os semestres com resultado abrem sozinhos.
- No card da disciplina, o selo "Minha disciplina" fica no canto superior direito, na mesma linha do nome.
- A página da disciplina mostra semestre e carga horária junto do link "Ver ementa oficial".
- O endereço é legível: `/estudos/estrutura-de-dados-iii`. O slug é calculado a partir do nome e não fica guardado no banco.

---

# 55. Estudos parte 2 — 02/11/2026

Decisões tomadas na definição da parte 2 de Estudos (spec `specs/2026-11-02-estudos-parte-2-design.md`).

## 55.1 Três etapas

A parte 2 é entregue em três etapas, cada uma funcionando de ponta a ponta:

1. **2a:** Materiais e Moderação de material.
2. **2b:** Meus conteúdos, Editar, Excluir e Cancelar envio.
3. **2c:** Notificações.

"Retirar material aprovado" e "solicitar revisão" ficam para uma rodada própria.

> **Atualização (04/11/2026):** a retirada administrativa de conteúdo aprovado foi classificada como pós-MVP (§63).

## 55.2 Autoria com opção de anônimo

- O material mostra o **nome de exibição** do autor.
- Com "Compartilhar anonimamente", os outros estudantes veem "Compartilhado pela comunidade", mas o sistema guarda o autor.
- A condição de autoria não muda numa edição.
- **Substitui** a §18 e o RF-39 (todo material anônimo, decisão adiada para o próximo semestre).
- **Na Moderação:**
  - o MODERADOR vê "Comunidade" no material anônimo, e o backend não envia o nome a ele;
  - o ADMINISTRADOR vê o nome, com a marca "Enviado anonimamente".
- Os Termos passam à versão **0.2**, com essa regra.

## 55.3 Regras do envio

- **Limites:** título até 200 caracteres, descrição até 2.000 e link até 2.048.
- **Links aceitos:** qualquer endereço `http` ou `https`. Quando falta o protocolo, o sistema completa com `https://`.
- **Link repetido:** recusado na mesma disciplina, se houver envio pendente ou aprovado com ele. A regra é garantida também no banco.
- **Limite:** 10 envios por hora por usuário.
- **Mostrar mais:** de 20 em 20 materiais.
- A disciplina em Estudos **não** mostra a quantidade de materiais.

## 55.4 Moderação

- **Motivo da rejeição obrigatório** para MODERADOR e ADMINISTRADOR, com até 500 caracteres. Substitui a §37 e o RF-73.
- **Fila sem filtro por tipo** enquanto houver um tipo só.
- **Status na tabela de materiais:** o status de moderação fica na própria tabela, sem tabela genérica de moderação.

## 55.5 Resolvidas para as próximas etapas

- **§52.5 (cancelamento):** o autor cancela o envio pendente na hora, e o envio passa a CANCELADO, que aparece só no filtro Todos.
- **§52.11 (rejeitado):** o material rejeitado não é reenviado. O autor faz um envio novo.
- **Código da solicitação:** `MAT-00042`.
- **Meus conteúdos:** fica em `/perfil/meus-conteudos`.
- **Alteração de material publicado:** é moderada mostrando a versão publicada e a proposta lado a lado.
- **Excluir com alteração pendente:** excluir o material cancela a alteração.
- **§33 (notificações):**
  - clicar marca a notificação como lida e leva ao conteúdo: uma aprovação abre a disciplina, e uma rejeição abre o item em Meus conteúdos;
  - o contador é consultado a cada 60 segundos;
  - quatro eventos geram notificação: material aprovado, material rejeitado, alteração aprovada e alteração rejeitada;
  - o painel mostra as 20 mais recentes.

## 55.6 Escolhas da etapa 2b (Meus conteúdos, Editar, Excluir e Cancelar)

- **Paginação:** Meus conteúdos tem "Mostrar mais" de 20 em 20 (decisão do Marcelo). O endereço `?item=MAT-00042` carrega as páginas seguintes até achar o item.
- **Alteração pendente não é cancelada:** o autor espera a análise, e uma segunda edição recebe "Já existe uma alteração deste material em análise.".
- **Alteração rejeitada:** aparece no item original como "Alteração não aprovada em dd/mm/aaaa: <motivo>", até a próxima alteração.
- **"Ver dados enviados"** fica disponível em todos os itens.
- **Ação não permitida:** responde 409 com "Este envio não pode ser cancelado.", "Este material não pode ser editado." ou "Este material não pode ser excluído.".
- **Na Moderação,** o envio cancelado pelo autor durante a análise mostra "Este conteúdo foi cancelado pelo autor".
- **Aprovação de alteração com link repetido:** se o link da alteração passou a repetir outro envio, a aprovação mostra o erro no próprio diálogo, e a alteração continua pendente para ser rejeitada.
- **Limite de envios:** os 10 envios ou edições por hora contam só os que deram certo. Erros de validação não gastam a cota.
- **Edição sem nenhuma mudança** é recusada com "Nenhuma alteração foi feita no material.".
- **Alteração única no banco:** a regra de uma só alteração pendente por material também é garantida no banco (índice parcial). Editar e excluir o mesmo material ao mesmo tempo acontecem um depois do outro (trava no registro).
- **Código nos erros 409:** os 409 de regra trazem um `code` (`material_cancelled`, `already_decided`, `link_taken`, `revision_pending`, `no_changes`, `not_editable`, `not_deletable`, `not_cancellable`). O front decide pelo código, nunca pelo texto.
- **Links equivalentes** contam como repetidos: a porta padrão (`:443` e `:80`) some, um endereço sem caminho ganha `/`, e domínios com acento são guardados na forma acentuada (`xn--…` e `münchen.de` viram o mesmo). A imagem de produção ganhou a extensão `intl` para isso.
- **Bytes nulos** são removidos do texto recebido em toda a API, porque o PostgreSQL recusa gravar esses bytes.

> **Atualização (04/11/2026):** os filtros de Meus conteúdos no MVP são só por situação (§60).

## 55.7 Escolhas da etapa 2c (Notificações)

- **Uma classe de notificação:** os quatro eventos usam uma só classe (`MaterialDecided`), que muda apenas o tipo. A spec citava quatro classes.
- **Mesma transação:** a notificação é criada junto com a decisão. Uma decisão recusada (409) não notifica.
- **Painel:** usa o `Popover` do shadcn e mostra "Não foi possível carregar as notificações agora." quando a lista falha.
- **Texto de sucesso do envio:** passa a ser "Você receberá uma notificação quando o material for analisado.", mantendo o link para Meus conteúdos.

---

# 56. Escopo do MVP: sem Oportunidade e sem Comunidade — 02/11/2026

Decisão do Marcelo: **Oportunidade** e **Comunidade** saem do MVP e ficam para a próxima versão.

## 56.1 O que sai

- **Oportunidade:** oportunidades do IFSul, "Indicar oportunidade" e a moderação de oportunidades.
- **Comunidade:** feed, publicações, "Compartilhar com a comunidade", canais e **busca de estudantes**. A busca fazia parte da Comunidade, então sai junto.
- Os tipos PUBLICAÇÃO e OPORTUNIDADE na fila de Moderação e nas notificações.
- No painel `/admin`: os cadastros curados de oportunidades do IFSul e de canais da Comunidade.

## 56.2 Consequências

- **Navegação do ALUNO:** Home, Estudos e Perfil. No grupo inferior: Configurações e Sair. MODERADOR e ADMINISTRADOR continuam vendo Moderação.
- **Atalhos da Home:** só Estudos e Perfil. Os endereços `/oportunidade` e `/comunidade` levam à Home.
- **Moderação:** fica só com materiais, e o filtro por tipo não é necessário no MVP.
- **Perfil público:** continua no MVP. Sem a busca de estudantes, o caminho até ele passa a ser o nome do autor nos materiais.
- **Limites de texto e "Mostrar mais":** os de oportunidade e publicação (pendências 2.7) e a quantidade de itens no feed e na busca (pendências 2.8) deixam de bloquear o MVP.
- **Identidade do projeto:** os pilares (Conexão, Conhecimento, Oportunidades, Comunidade) e os textos institucionais continuam, porque descrevem o ConectaCC como um todo, e não as telas do MVP.

## 56.3 Retomada da Comunidade (04/11/2026)

A Comunidade permanece fora do escopo obrigatório do MVP e não deve bloquear as entregas atuais. Porém, caso haja capacidade durante o desenvolvimento ou os testes, poderá ser retomada ainda nesta etapa como funcionalidade adicional.

Se isso acontecer, os fluxos e as informações exibidas — como descoberta de estudantes, cards de Perfil, disciplinas atuais, "Pode ajudar com" e "Procura ajuda com" — deverão ser definidos antes da implementação.

O Perfil público deve continuar funcionando independentemente da existência da Comunidade.

---

# 57. Perfil público — 04/11/2026

Decisões tomadas na definição do Perfil público (spec `specs/2026-11-04-perfil-publico-design.md`).

## 57.1 O que é

"Perfil público" significa **visível para outros usuários autenticados no ConectaCC**, não público para a internet.

- **Aparecem:** Nome de exibição, avatar pelas iniciais, Curso, Disciplinas atuais e, quando preenchidos, Sobre mim, Interesses, Pode ajudar com, Procura ajuda com, GitHub e LinkedIn.
- **Nunca aparecem:** e-mail institucional, Nome completo da conta, dados de autenticação, informações administrativas, Meus conteúdos e histórico privado.
- **Semestre** fica de fora. Será tratado junto das regras de visibilidade.
- **Materiais da pessoa** não são listados no perfil.
- **Disciplinas atuais** ficam visíveis para estudantes autenticados e não há opção de escondê-las no MVP.

## 57.2 Como se chega ao perfil

Material identificado → clicar no Nome de exibição do autor → Perfil público. O material anônimo continua com "Compartilhado pela comunidade" e não dá acesso ao perfil real do autor: a API não envia o código dele.

O próprio nome num material leva a `/perfil`. "Ver como outros estudantes veem meu perfil" fica para o futuro.

Se houver tempo, a Comunidade pode voltar ainda durante a etapa de testes, criando novas formas de descobrir estudantes (ver §56).

## 57.3 Código público na URL

A URL usa um **código público aleatório e não sequencial** (`/estudantes/<código>`), e não o id interno, por causa da evolução futura para perfil oculto. O código não substitui autorização: se um perfil puder ser ocultado no futuro, conhecer a URL não poderá dar acesso a ele.

## 57.4 Navegação

O link "Voltar" retorna à página anterior, ou a Estudos quando o perfil foi aberto direto pelo endereço. Perfil inexistente mostra "Este perfil não está disponível." com "Voltar para Estudos".


---

# 58. Cores dos status — 04/11/2026

Decisão de Produto para manter uma semântica única e evitar interpretações diferentes entre telas.

## 58.1 A regra

| Status ou tag | Cor |
|---|---|
| PENDENTE / EM ANÁLISE / EM REVISÃO | amarelo |
| APROVADO | verde |
| REJEITADO | vermelho |
| EXCLUÍDO / RETIRADO | vermelho |
| CANCELADO | cinza neutro |
| EDITADO / ALTERAÇÃO | laranja |

- **Amarelo:** algo ainda aguarda decisão ou análise.
- **Verde:** resultado positivo.
- **Vermelho:** resultado negativo ou conteúdo retirado.
- **Cinza:** fluxo encerrado de forma neutra e voluntária pelo próprio usuário.
- **Laranja:** houve modificação, sem substituir o status principal de Moderação.

## 58.2 Alteração é indicação adicional

"Editado/Alteração" não substitui PENDENTE, APROVADO ou REJEITADO. Um material pode continuar **Aprovado** e ter também a tag **Alteração** quando houver edição relacionada.

## 58.3 Onde vale

- **Meus conteúdos:** o status de cada item e a tag Alteração quando o item tem alteração em análise ou não aprovada.
- **Moderação:** Pendente em amarelo e Alteração em laranja, na fila e no painel de análise.
- **Detalhes do envio, histórico, Perfil público, painel `/admin` e módulos futuros:** se o mesmo status aparecer, vale a mesma cor.

A informação não depende só da cor: a tag sempre traz o texto. No front, a regra fica em um único componente, `StatusTag`.

Isso muda o que estava implementado: EXCLUÍDO era cinza e Alteração era neutra. No painel de análise, o destaque de "Alteração proposta" deixa de ser azul e passa a laranja **(escolha do Claude)**.

---

# 59. Cores das Notificações — 04/11/2026

Revisão de Produto do painel de Notificações. Todo o comportamento de §55.5 e §55.7 continua igual: clicar marca como lida e leva ao conteúdo, "Marcar todas como lidas", contador até `9+`, mais recentes primeiro, lidas continuam no painel, indicação "Nova", tempo relativo e polling.

## 59.1 O que muda

- **Contador do sino:** `#C65A2E`, no lugar do vermelho `#B42318`.
- **Notificação não lida:** fundo `#D9E9F8`, no lugar de `#EEF7FC`. Depois de lida, volta ao fundo normal do painel.
- **Aprovação e rejeição:** os ícones ✓ e ✕ não têm mais fundo verde ou vermelho. A apresentação é neutra e o resultado é comunicado pelo ícone, pelo título e pelo texto.

A única cor de fundo especial do item da lista é a de não lida. Ao passar o mouse, a não lida fica um pouco mais clara e mantém o tom azul **(escolha do Claude)**.

> **Atualização (04/11/2026):** a cor do contador passou a ser `#BD5228` (§62.1).

## 59.2 Ponto de atenção

O número branco sobre `#C65A2E` tem contraste de 4,28:1, abaixo do mínimo de 4,5:1 (WCAG AA) para texto pequeno. A cor foi aplicada como definida. Leitores de tela não dependem dela, porque o botão anuncia a quantidade de não lidas.

> **Atualização (04/11/2026):** resolvido com a troca da cor (§62.1).

---

# 60. Filtros de Meus conteúdos — 04/11/2026

Revisão de Produto. A implementação atual está correta e não muda. Esta decisão resolve o ponto do filtro por tipo que estava em aberto em §52.11.

## 60.1 O que fica no MVP

Só os filtros por situação: **Todos**, **Em andamento**, **Aprovados**, **Rejeitados** e **Excluídos**.

## 60.2 O que fica para o futuro

O **filtro por tipo de conteúdo** e a **busca textual** ficam de fora de propósito. Não são funcionalidades esquecidas.

No escopo atual, Meus conteúdos trabalha só com Material, então um filtro por tipo não teria utilidade. Se outros tipos de conteúdo entrarem nessa área, a necessidade será revista.

---

# 61. Configurações — 04/11/2026

Decisões tomadas na definição de Configurações (spec `specs/2026-11-04-configuracoes-design.md`).

## 61.1 Outras sessões

Trocar a senha encerra as outras sessões da conta, e a pessoa continua logada no aparelho atual. Se a recuperação de senha entrar depois, ela também encerra as outras sessões. Isso resolve a pendência 2.5.

## 61.2 Regras da troca

- **Nova senha igual à atual** é aceita. As regras continuam sendo as de §30: 8 caracteres e confirmação igual.
- **Limite:** 5 tentativas por minuto, por usuário, com a mesma mensagem do login.

## 61.3 O que fica de fora

- **E-mail avisando da troca de senha:** não é enviado no MVP. Fica para decisão futura (`pendencias.md` 1.7).
- **"Esqueci minha senha":** continua opcional (§52.9) e será reavaliado depois do painel `/admin`. Até lá, quem esquecer a senha não consegue recuperá-la pelo app, inclusive durante o piloto.

## 61.4 Escolhas da implementação

- **Sessão atual:** o id muda na troca e a linha antiga é apagada na hora. O `AuthenticateSession` já recusaria um cookie antigo pela divergência do hash da senha; apagar a linha é uma camada extra.
- **Limite conhecido:** se outra requisição do mesmo aparelho estiver em andamento durante a troca (o sino de notificações ou outra aba), a resposta dela pode devolver o cookie antigo e deslogar a pessoa. É raro e nada se perde: a senha nova já vale. Será revisto se houver relato no piloto.
- **Sair durante o envio:** o envio não é cancelado, então "Descartar alterações?" espera o resultado. Se a senha mudou, a pessoa fica na página e vê o aviso. Se falhou, o diálogo aparece **(escolha do Claude)**.
- **Link "Configurações" na própria página:** não pergunta nada e mantém os campos, porque não há saída **(escolha do Claude)**.
- **Foco:** depois do sucesso vai para o aviso; ao fechar o aviso, vai para "Senha atual" **(escolha do Claude)**.
- **E-mail na API:** `email` entra na resposta de login e de `GET /me`, que só devolvem os dados da própria pessoa. O Perfil público continua sem ele.
- **E-mail ainda carregando ou indisponível:** a página mostra um esqueleto durante o carregamento e "E-mail indisponível no momento." se a busca falhar **(escolha do Claude)**.
- **Aviso de sucesso:** usa o botão de fechar que já existe ("Fechar aviso") **(escolha do Claude)**.
- **Página "Em construção":** removida, porque Configurações era a última área que a usava.

---

# 62. Ajustes das cores de status e das Notificações — 04/11/2026

Decisões de Produto sobre os dois pontos abertos das §58 e §59.

## 62.1 Cor do contador do sino

O contador passa de `#C65A2E` para `#BD5228`. Isso substitui a cor definida em §59.1.

Com texto branco, `#C65A2E` tem contraste de 4,28:1, abaixo do mínimo de 4,5:1 (WCAG AA) para texto pequeno. `#BD5228` chega a 4,77:1 e mantém praticamente a mesma identidade visual.

## 62.2 Status e confirmação de ação são coisas diferentes

- **Status REJEITADO:** vermelho em tags, badges e demais representações do status (§58).
- **Ação concluída com sucesso:** aviso verde.

Depois que a Moderação rejeita um conteúdo, o aviso "Conteúdo rejeitado." continua verde, porque confirma que a ação deu certo. Em vermelho, ele poderia ser confundido com uma falha ao rejeitar.

---

# 63. Retirada administrativa de conteúdo aprovado — 04/11/2026

Decisão de Produto. **Classificação: evolução futura / pós-MVP.**

A retirada administrativa de conteúdo já aprovado/publicado pelo ADMINISTRADOR permanece prevista, mas é uma funcionalidade pós-MVP. O MVP pode ser considerado concluído sem esse fluxo.

No futuro, o ADMINISTRADOR poderá retirar um conteúdo publicado quando houver necessidade administrativa, por exemplo:

- conteúdo aprovado por engano;
- informação posteriormente identificada como incorreta;
- link inválido ou inadequado;
- conteúdo desatualizado;
- problema identificado após a publicação;
- outras situações administrativas justificadas.

## 63.1 Regra atual para o MVP

Não implementar agora:

- botão de retirada administrativa;
- endpoint específico para retirada;
- fluxo de reversão de aprovação;
- notificações relacionadas;
- estados administrativos adicionais decorrentes dessa ação;
- tela específica para esse processo.

O `/admin` do MVP não precisa ter essa funcionalidade.

## 63.2 Regra futura

Quando essa funcionalidade entrar no escopo:

- somente o ADMINISTRADOR poderá realizar a retirada administrativa;
- o conteúdo deverá deixar de ficar disponível publicamente;
- a aprovação anterior não deverá ser apagada nem sobrescrita silenciosamente;
- a ação de retirada deverá ficar registrada nos logs/histórico de auditoria;
- deverá ser preservado o registro de que o conteúdo havia sido aprovado;
- o MODERADOR não poderá retirar diretamente um conteúdo publicado.

## 63.3 O que será definido na implementação futura

- necessidade de justificativa obrigatória;
- status final utilizado, como RETIRADO ou equivalente;
- possibilidade ou não de restaurar o conteúdo;
- notificação ao autor;
- impacto em Meus conteúdos;
- informações exibidas no histórico;
- comportamento caso exista uma alteração pendente;
- regras específicas de auditoria.

## 63.4 Outras ações sobre decisões de Moderação

Também ficam como evolução futura / pós-MVP:

- **MODERADOR:** solicitar revisão ou remoção de conteúdo já publicado;
- **ADMINISTRADOR:** revisar decisões de Moderação;
- **ADMINISTRADOR:** reverter rejeições.

O MVP e o `/admin` podem ser considerados concluídos sem esses fluxos. As regras deles serão definidas quando entrarem no escopo.

> **Atualização (08/11/2026):** a regra principal da reversão de rejeição e da solicitação de revisão (agora também por usuários autenticados, como denúncia) foi decidida na §67. Os dois fluxos continuam pós-MVP, e só os detalhes de UX e de implementação ficam para depois.

---

# 64. Exclusão de conta: política do piloto — 05/11/2026

Complementa a §29, responde a `pendencias.md` 3.2 e detalha o RF-61.

## 64.1 Classificação

A política de exclusão de conta é necessária antes do piloto e fica definida nesta seção.

**A implementação fica fora do MVP.** Isso vale para a autoexclusão pelo próprio usuário e para a ação de exclusão no painel `/admin`.

Não implementar agora:

- botão ou ação "Excluir conta" no `/admin`;
- tela de autoexclusão;
- endpoint de exclusão;
- fila de pedidos de exclusão.

## 64.2 Fluxo inicial

1. A pessoa solicita a exclusão por e-mail ou canal de contato.
2. O ADMINISTRADOR executa a exclusão administrativamente.

O canal de contato ainda precisa ser confirmado.

## 64.3 Dados da conta

Ao excluir a conta, devem ser removidos ou desvinculados os dados pessoais da conta, incluindo:

- nome;
- e-mail;
- senha;
- perfil;
- disciplinas atuais;
- sessões ativas;
- notificações;
- demais dados pessoais associados à conta.

A conta deixa de existir como conta utilizável. O Perfil público deixa de existir. O mesmo e-mail poderá fazer um novo cadastro futuramente.

## 64.4 Materiais

- **Solicitações pendentes:** são canceladas.
- **Materiais aprovados ou publicados:** permanecem publicados. A autoria deixa de estar vinculada à conta excluída, e o material passa a aparecer como **"Compartilhado pela comunidade"**.

**Motivo:** preservar o conteúdo útil aos colegas sem manter a identificação pessoal do autor excluído.

## 64.5 Observações

- A política deve ser refletida nos Termos (`pendencias.md` 3.1).
- A implementação e a redação final devem ser avaliadas à luz da LGPD e das orientações institucionais.
- É uma política do piloto e pode ser refinada depois.
- A autoexclusão pelo próprio usuário pode ser tratada futuramente, se necessário.

## 64.6 Ainda em aberto

- qual é o canal de contato;
- como atender um pedido durante o piloto, já que a ação não será implementada no MVP (`pendencias.md` 3.2);
- por quanto tempo algum dado precisa ser retido;
- o que acontece com materiais rejeitados, cancelados ou já excluídos da conta.

---

# 65. Painel `/admin` — 05/11/2026

Decisões da entrega do painel (spec `specs/2026-11-05-painel-admin-design.md`).

## 65.1 O que o painel cobre

- **Usuários:** lista, busca, filtro por papel e alteração de papel.
- **Moderações:** consulta somente leitura de todos os materiais, inclusive excluídos e alterações.

Ficam fora: exclusão de conta (§64), retirada, revisão e reversão de decisões (§63), catálogo de disciplinas, tela inicial com contadores, edição de nome e e-mail, redefinição de senha e tela de auditoria.

## 65.2 Papéis

- O ADMINISTRADOR define ALUNO, MODERADOR ou ADMINISTRADOR em outras contas, com confirmação.
- Ninguém muda o próprio papel. Assim, quem está usando o painel não se tranca para fora.
- O workflow "Promover usuário" continua como saída de emergência e para criar o primeiro ADMINISTRADOR.

## 65.3 Acesso

- O painel tem login próprio em `/admin/login` e usa a mesma sessão do app.
- Sair do painel também encerra o app.
- ALUNO e MODERADOR recebem a página "Você não tem permissão para acessar esta página.".
- O papel é conferido a cada requisição.

## 65.4 Autoria nas Moderações

Vale a §55.2: o ADMINISTRADOR vê o nome de exibição do autor, com a marca "Enviado anonimamente". O MODERADOR não acessa o painel.

## 65.5 Escolhas do Claude

Estas escolhas não foram decididas pelo time e podem ser revistas:

- **Tabelas:** filtro por papel em Usuários; mais recentes primeiro nas duas telas; empilhadas no celular.
- **Situação:** "Alteração" aparece como uma marca ao lado da situação, em Moderações, conforme a §58.
- **Login:** título "Painel administrativo" e botão "Entrar". Sem "Lembrar de mim", como no app.
- **Painel:** sem modo escuro, busca global e notificações em banco. O rodapé da sidebar tem "Voltar ao ConectaCC".
- **Link no app:** "Painel administrativo" abre na mesma aba.
- **Log:** cada mudança de papel gera uma linha só com IDs internos e os papéis.
- **Dois ADMINISTRADORES ao mesmo tempo:** se um retirar o papel do outro no mesmo instante, o sistema pode ficar sem ADMINISTRADOR. O caso é raro, e o workflow resolve.

## 65.6 Layout do painel

O painel segue o layout do resto do sistema: sidebar azul-marinho, cabeçalho branco com o nome da pessoa, fundo claro, fontes e tags de status iguais às do app.

As tabelas não têm rolagem lateral. Para isso, as colunas foram agrupadas:

- **Moderações:** "Material" reúne título, código e disciplina, e "Decisão" reúne quem decidiu e a data.
- **Usuários:** "Nome" reúne nome e nome de exibição.

"Enviado em" e "Cadastro" só aparecem em telas a partir de 1280 px. O detalhe de Moderações continua mostrando todos os campos, e a data e a hora do envio.

## 65.7 Avatar sem serviço externo

O avatar padrão do Filament é buscado em `ui-avatars.com`, o que enviaria as iniciais da pessoa para fora. O painel desenha o avatar localmente, com as mesmas iniciais do app.

---

# 66. Auditoria — 07/11/2026

Decisões da auditoria do `/admin` (spec `specs/2026-11-07-auditoria-design.md`).

## 66.1 O que significa "revisar os logs"

O ADMINISTRADOR revisa as ações na tela **Auditoria** do `/admin`. Ela é o registro oficial do que foi feito no sistema e por quem. A linha `Papel alterado` continua no log técnico do Render, só para operação.

## 66.2 O que é registrado

Toda ação que muda dados gera um evento, na mesma transação da ação:

- cadastro;
- troca de senha;
- papel alterado, pelo painel ou pelo workflow;
- material enviado;
- alteração enviada;
- envio cancelado;
- material excluído;
- aprovado;
- rejeitado.

Login, logout e consultas não são registrados. Isso inclui o ADMINISTRADOR ver o autor de um material anônimo.

## 66.3 O que o evento guarda

O evento guarda:

- quem fez;
- a ação;
- a conta ou o material afetado;
- a data;
- um resumo da mudança, com o título que o material tinha naquele momento;
- se o envio era anônimo e se era uma alteração;
- na aprovação de uma alteração, a versão publicada antes.

**Nunca guarda:** IP, e-mail, senha, hash, token, sessão ou o texto do motivo da rejeição.

## 66.4 Quem vê

Só o ADMINISTRADOR, na tela Auditoria, com filtros por ação, pessoa e período. O MODERADOR não vê.

O envio anônimo mostra quem enviou, com a marca "Enviado anonimamente", como em Moderações (§55.2). A marca aparece só abaixo do autor, e não de quem moderou.

## 66.5 Imutabilidade e retenção

- **Imutabilidade:** uma trigger do PostgreSQL recusa alterar, apagar ou esvaziar eventos, inclusive pelo painel ou por um comando manual. Remover um evento exige uma migration deliberada.
- **Retenção:** os eventos ficam guardados durante o piloto. Quando a exclusão de conta for implementada (§64), o evento continua existindo e a pessoa aparece como "Conta excluída". A regra final de retenção entra na revisão dos Termos e da LGPD.
- **Histórico anterior:** não foi reconstruído, e a auditoria conta a partir do deploy.

## 66.6 Workflow

A troca de papel pelo workflow "Promover usuário" aparece como "Workflow do GitHub".

## 66.7 Regra para entregas futuras

Toda ação administrativa sensível nova (reversão de rejeição, retirada de conteúdo, exclusão de conta) só entra junto com o evento de auditoria dela.

## 66.8 Horário do painel

O banco guarda as datas em UTC. O painel mostra as datas e filtra o período no horário de Brasília (`America/Sao_Paulo`), como o app já faz no navegador.

Isso corrige as datas de Usuários e Moderações, que apareciam três horas adiantadas desde a §65.

---

# 67. Reversão de rejeição e denúncia de conteúdo — 08/11/2026

Duas evoluções **pós-MVP**, já com a regra de negócio decidida. Elas não entram no checklist obrigatório do MVP.

Esta seção substitui, para esses dois fluxos, a frase final da §63.4 ("As regras deles serão definidas quando entrarem no escopo") e, para a denúncia, a §52.10 ("as regras delas não estão definidas"). A revisão geral de decisões de Moderação pelo ADMINISTRADOR continua como está na §63.4.

## 67.1 Reversão de rejeição pelo ADMINISTRADOR

**Regra decidida:** o ADMINISTRADOR poderá reverter uma rejeição diretamente, de **REJEITADO** para **APROVADO**. O conteúdo não precisa voltar para PENDENTE antes da nova aprovação.

- É uma funcionalidade pós-MVP.
- Só o ADMINISTRADOR pode reverter. O MODERADOR não pode.
- A rejeição anterior não pode ser apagada nem sobrescrita silenciosamente.
- O histórico precisa preservar que o conteúdo já havia sido rejeitado.
- A reversão precisa ser registrada na auditoria (§66.7).

**Fica para quando for implementada:**

- a UX do fluxo;
- se a justificativa será obrigatória;
- as notificações;
- as informações exibidas ao autor.

## 67.2 Denúncia e solicitação de revisão de conteúdo publicado

**Regra decidida:**

- Usuários autenticados poderão **denunciar conteúdo** ou pedir que ele seja revisado.
- O MODERADOR também poderá solicitar revisão ou remoção. Isso amplia a §63.4, que citava só o MODERADOR.
- Uma denúncia ou solicitação **não remove o conteúdo automaticamente**.
- A decisão de retirar um conteúdo publicado pertence ao ADMINISTRADOR.
- A retirada administrativa continua pós-MVP (§63).

**Fica para quando for implementada:**

- as categorias ou os motivos da denúncia;
- se a justificativa será obrigatória ou opcional;
- quem recebe a solicitação;
- o comportamento para denúncias repetidas;
- a possibilidade de cancelar uma denúncia;
- as notificações;
- o tratamento de conteúdo anônimo;
- o registro no histórico e na auditoria;
- os resultados possíveis da análise;
- uma eventual relação com sanções ou bloqueios.

---

# 68. Comunidade: mural de materiais — 08/11/2026

Decisões da volta da Comunidade como mural (spec `specs/2026-11-08-comunidade-mural-design.md`). É a retomada prevista na §56.3: funcionalidade adicional, fora do escopo obrigatório do MVP.

## 68.1 O que é

A Comunidade volta como **camada de descoberta**: um mural cronológico do que foi compartilhado no curso.

O mural não guarda cópia de nada. Estudos é dono do material, e a Comunidade só aponta para ele, consultando os materiais aprovados.

Ficam fora desta versão: tópicos, respostas, comentários, curtidas, posts de texto livre, canais e busca de estudantes.

## 68.2 O que aparece

- **O que gera uma publicação:** a primeira aprovação de um material. Uma alteração aprovada não gera outra publicação, e o mural mostra o título atual.
- **Exclusão:** material excluído sai do mural.
- **Ordem:** mais recentes primeiro, pela data de publicação, com o tempo relativo. O mural carrega 20 por vez, com "Mostrar mais".

## 68.3 Autoria

- **Material identificado:** "Marcos compartilhou “Resumo de Grafos” em Estrutura de Dados II". O nome leva ao Perfil público, ou a `/perfil` quando é a própria pessoa.
- **Material anônimo:** "Comunidade compartilhou…", sem link de perfil, para todo mundo, inclusive o próprio autor e o ADMINISTRADOR. A API não envia o autor (§55.2 e §57.2).

## 68.4 Links

- **Título:** abre a disciplina já rolada até o material, com um destaque rápido. Se o material estiver mais abaixo na lista, a página carrega os próximos itens até achá-lo.
- **Disciplina:** abre a página da disciplina.

## 68.5 Navegação

"Comunidade" volta à sidebar, entre Estudos e Perfil, e `/comunidade` abre o mural. A Home não muda.

## 68.6 Próxima entrega

Oportunidades entra integrada ao mural, sempre com "Comunidade" como autoria pública. Os filtros "Tudo", "Materiais" e "Oportunidades" chegam junto com ela.

## 68.7 Escolhas do Claude

- **Paginação por cursor:** um material aprovado enquanto a pessoa lê não repete nem esconde itens em "Mostrar mais".
- **Subtítulo:** "Veja o que a comunidade de Ciência da Computação tem compartilhado.".
- **Estado vazio:** "Quando um material for aprovado em Estudos, ele aparece aqui.", com o link "Ir para Estudos".
- **Avatar:** as iniciais no material identificado e um ícone neutro de pessoas no anônimo.

## 68.8 Visual a partir da imagem de referência (08/11/2026)

O time enviou uma imagem de referência para a página, e a spec ganhou a seção 9.

**Entram:**

- **Cabeçalho:** faixa com ícone, sem ilustração.
- **Card:** disciplina, data, tag "Material" e ícone de documento.
- **Busca:** no título, na disciplina e no autor, sem acentos.
- **Ordem:** mais recentes ou mais antigos.
- **Visualização:** em lista ou em grade.
- **Card "Contribua com a comunidade":** o botão leva a Estudos.
- **"A comunidade em números":** com materiais e estudantes.

**Regra de privacidade:** a busca pelo nome do autor nunca encontra material anônimo.

**Ficam fora:** salvar e o menu "⋯". **Entram com Oportunidades:** as abas por tipo, "Explorar por tipo" e o número de oportunidades.

**Ícone do tipo:** por enquanto, todo material usa o ícone de documento.

---

# 69. Oportunidades no mural: filtro e "Compartilhar" — 08/11/2026

Decisões antecipadas para a entrega de Oportunidades, que entra integrada ao mural da Comunidade (§68.6).

## 69.1 Filtro, não abas

O mural **não terá abas** por tipo. A separação entre materiais e oportunidades será feita por um **filtro**, no mesmo estilo da ordem que já existe ("Mais recentes" e "Mais antigos").

As opções previstas são "Todos", "Materiais" e "Oportunidades". O filtro se combina com a busca e com a ordem, e entra no endereço da página como os demais.

Isso substitui as "abas por tipo" da §68.8 e o "Os filtros 'Tudo', 'Materiais' e 'Oportunidades'" da §68.6, que passam a ser este filtro.

## 69.2 "Compartilhar" para material e oportunidade

O botão do card "Contribua com a comunidade" passa a servir para os dois tipos. A pessoa escolhe se vai compartilhar um **material** ou uma **oportunidade** (vaga, estágio, evento, hackathon e afins).

Hoje o botão é "Compartilhar material" e leva a Estudos.

O fluxo será planejado no design de Oportunidades. Ficam para essa rodada:

- o nome do botão e onde a escolha do tipo aparece (menu, diálogo ou página);
- como escolher a disciplina de um material a partir da Comunidade, já que hoje o envio fica dentro da disciplina;
- se Estudos continua com o próprio "Compartilhar material" na página da disciplina;
- os campos e as categorias da oportunidade;
- a Moderação da oportunidade e as notificações;
- a autoria: a oportunidade aparece sempre como "Comunidade" no mural (§68.6), e falta definir se o envio de material a partir da Comunidade mantém a opção "Compartilhar anonimamente".

## 69.3 O que continua em aberto

Também ficam para o design de Oportunidades:

- "Explorar por tipo";
- o número de oportunidades em "A comunidade em números";
- o ícone de cada tipo no card.

---

# 70. Oportunidades — 08/11/2026

Decisões da entrega de Oportunidades (spec `specs/2026-11-08-oportunidades-design.md`). Ela volta como funcionalidade adicional, fora do escopo obrigatório do MVP, como a Comunidade (§56.3), e já integrada ao mural (§68 e §69).

## 70.1 O que é

Oportunidade reúne indicações de estágios, empregos, eventos e projetos. Ela guarda só os metadados e o link, nunca uma cópia da página externa, e não faz raspagem (§53.1). O ConectaCC funciona como centralizador e ponte.

## 70.2 Tipos e campos

- **Tipos:** os cinco do RF-43 e da §22, sem criação livre: Estágio, Emprego, Evento, Projeto e Outro.
- **Campos obrigatórios:**
  - Tipo;
  - Título, com até 200 caracteres;
  - Descrição curta, com até 300 caracteres;
  - Link;
  - Modalidade: Remoto, Híbrido ou Presencial.

  Isso substitui a "Descrição opcional" do RF-42.
- **Link:** segue a validação do material e recusa um link já ativo em outra oportunidade.

## 70.3 Prazo

"Inscrições até" é opcional. Se ficar vazio, a oportunidade fica visível por **15 dias depois da aprovação**. A contagem começa na aprovação, porque só a partir daí a oportunidade aparece **(escolha do Claude)**.

A oportunidade continua visível até o fim do último dia, no horário de Brasília. Depois disso, sai da página, do mural e dos números, sem ação de ninguém, e aparece como "Encerrada" em Meus conteúdos.

## 70.4 Autoria

O sistema registra quem enviou. MODERADOR e ADMINISTRADOR veem essa pessoa na análise e no `/admin`.

**Nenhuma área pública mostra autoria:** nem o nome, nem "Publicado por Comunidade". No mural, a frase é "Comunidade adicionou uma nova oportunidade em [Tipo]: [Título]".

Não há envio anônimo. O MODERADOR não analisa a própria oportunidade, como no material.

## 70.5 Envio, Moderação e autor

- **Envio:** pelo botão "+ Adicionar oportunidade" ou pelo menu "Compartilhar conteúdo" da Comunidade. Vale o mesmo limite por hora do material, e os dois tipos somam.
- **Moderação:** a fila mistura os dois tipos, com a etiqueta do tipo e sem filtro. Aprovar e rejeitar com motivo seguem as regras do material.
- **Autor:** acompanha em Meus conteúdos, com o filtro de tipo "Materiais" ou "Oportunidades". Pode cancelar enquanto a oportunidade está pendente e excluir depois de aprovada. Não há edição.
- **Notificações:** a aprovação leva à oportunidade na página, e a rejeição leva ao item em Meus conteúdos.

## 70.6 Página

- **Navegação:** "Oportunidade" fica no singular (§6), entre Estudos e Comunidade.
- **No topo:** o card fixo "Conheça as oportunidades de bolsas do IFSul", que leva a `https://www.ifsul.edu.br/estudante-pronatec` (link confirmado pelo time).
- **Lista:** busca sem acentos no título e na descrição, filtro por tipo e cards em grade.

## 70.7 Mural e "Compartilhar"

- **Mural:** mistura materiais e oportunidades, com o filtro "Tipo" (§69.1).
- **"A comunidade em números":** ganha "Oportunidades" e passa a ser uma lista, para caber na coluna lateral.
- **Botão:** "Compartilhar conteúdo" abre um menu com "Material", que leva a Estudos, e "Oportunidade", que abre o modal **(escolha do Claude para o nome do botão, em aberto na §69.2)**.

## 70.8 Auditoria e `/admin`

- **Eventos:** "Oportunidade enviada", "Oportunidade cancelada", "Oportunidade excluída", "Oportunidade aprovada" e "Oportunidade rejeitada". O título e o tipo ficam no evento.
- **`/admin` → Oportunidades:** só de leitura, com quem enviou, a situação (inclusive "Encerrada") e a decisão.

## 70.9 Visual da página (08/11/2026)

A página segue uma imagem de referência do time:

- **Título:** "Oportunidades", no plural, com o item da navegação ainda no singular (§6).
- **Cabeçalho:** traz o card das bolsas do IFSul, com o botão "Ver oportunidades".
- **Estado vazio:** novo texto.

Os tipos ficam num **filtro de seleção**, e não em botões no estilo de abas, mantendo os cinco tipos. O logo do IFSul só entra com o arquivo oficial; até lá, o card usa um ícone.

