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

## 52.11 Pendências que continuam

- Cancelamento: fluxo, se é imediato, quem processa, representação e filtro.
- Rejeitado: se poderá ser editado e reenviado direto de Meus conteúdos ou se exige um novo envio.
- Filtro por tipo em Meus conteúdos: avaliar se for simples. Busca textual fica para o futuro.
- Continuam futuros: histórico de versões, linha do tempo, comparação antes/depois, auditoria completa, mostrar ao usuário comum quem moderou, portfólio, métricas, reavaliação e restauração pelo usuário.

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

