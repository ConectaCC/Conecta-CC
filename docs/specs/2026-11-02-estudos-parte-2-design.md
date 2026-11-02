# Estudos (parte 2): Materiais, Moderação, Meus conteúdos e Notificações — design

Data: 02/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o merge de Estudos etapa 1, PR #4)

Este documento registra o design da parte 2 de Estudos. Ele parte de:

- `requisitos.md`: §1.2 e §1.3 (papéis) e RF-35 a RF-39.1, RF-66 a RF-75;
- `decisoes.md`: §33 a §40, §51, §52 e §54;
- `fluxos.md`: seções 9 e 10;
- a spec da etapa 1 (`2026-10-31-estudos-design.md`), cujas convenções continuam valendo.

**Referência visual:**

- Protótipo congelado: artboards `Disciplina`, `Disc-*`, `Moderacao`, `Mod-*` e `Home-Notificacoes`.
- Esboços aprovados nesta conversa para as telas sem protótipo: formulário com anônimo, card com autoria e menu ⋯, Meus conteúdos e alteração na Moderação.
- **O protótipo da Moderação é anterior às decisões 51 e 52.** Onde os dois diferem, valem os documentos e este design.

Itens marcados **(escolha do Claude)** não foram decididos pelo Marcelo e podem ser trocados na revisão.

## 1. Intenção

A etapa 1 deixou Estudos navegável. Esta parte faz Estudos cumprir o que promete: o estudante compartilha um material, a moderação analisa, e o material aprovado aparece na disciplina. O autor acompanha o que enviou, corrige e exclui, e é avisado do resultado.

## 2. Etapas de entrega

São três etapas, implementadas e mergeadas nesta ordem. Cada uma entra na `main` funcionando de ponta a ponta (decisão do Marcelo, 02/11/2026):

| Etapa | Branch | Entrega |
|---|---|---|
| **2a** | `feat/materiais` | Compartilhar material (identificado ou anônimo), materiais aprovados na disciplina e Moderação de material |
| **2b** | `feat/meus-conteudos` | Meus conteúdos, Editar e Excluir (menu ⋯) e Cancelar envio |
| **2c** | `feat/notificacoes` | Sino, contador e painel de notificações |

Meus conteúdos vem antes das Notificações porque é onde o autor vê o motivo da rejeição (§52.2). Cada etapa tem o seu plano em `docs/plans/`.

### Não entra em nenhuma etapa

- "Retirar material já aprovado" (ADMINISTRADOR) e "solicitar revisão" (MODERADOR). Os fluxos ainda serão definidos numa rodada própria (`requisitos.md` §1.3).
- Filtro por tipo na fila de Moderação. Ele volta quando existir um segundo tipo (Publicação ou Oportunidade).
- Contador de materiais no card da disciplina em Estudos (decisão do Marcelo).
- Link do nome do autor para o Perfil público, que ainda não existe.
- Material no feed da Comunidade (RF-49) e a página de notificações (§33).
- Upload de arquivo. Material continua sendo só link.

## 3. Decisões do Marcelo (01/11 e 02/11/2026)

1. **Autoria com opção de anônimo.** O material mostra o nome de exibição do autor. No formulário há a opção "Compartilhar anonimamente": com ela marcada, os outros estudantes veem "Compartilhado pela comunidade", mas o sistema guarda quem enviou.
   - Isso **substitui** RF-39 e `decisoes.md` §18, que mantinham todo material anônimo e adiavam a decisão para o próximo semestre.
   - A condição de autoria é escolhida no envio e não muda numa edição (§52.4).
2. **Na Moderação:**
   - material identificado: MODERADOR e ADMINISTRADOR veem o nome;
   - material anônimo: o MODERADOR vê "Comunidade", e o ADMINISTRADOR vê o nome com a marca "Enviado anonimamente".
   - O backend não envia o nome ao MODERADOR. Não basta esconder na tela.
3. **Motivo da rejeição obrigatório** para MODERADOR e ADMINISTRADOR, com até 500 caracteres. Isso substitui RF-73 e o "(opcional)" do protótipo.
4. **Link repetido:** um link que já existe na mesma disciplina, com envio pendente ou aprovado, é recusado.
5. **Links aceitos:** qualquer endereço `http` ou `https`. Quando falta o protocolo, o sistema completa com `https://`.
6. **Limite de envios:** 10 por hora por usuário.
7. **Mostrar mais:** a lista mostra 20 materiais e carrega mais 20 a cada clique.
8. **Status de moderação na própria tabela de materiais.** Não há tabela genérica de moderação.
9. **Meus conteúdos:**
   - fica em `/perfil/meus-conteudos`;
   - o código da solicitação tem o formato `MAT-00042`;
   - o autor cancela o próprio envio pendente na hora, e o envio passa a CANCELADO;
   - um material rejeitado não é reenviado: o autor faz um envio novo;
   - a fila de Moderação mostra a alteração de um material publicado com a versão atual e a proposta lado a lado;
   - excluir um material com alteração pendente cancela a alteração.
10. **Notificações:**
    - clicar marca como lida e leva ao conteúdo: uma aprovação abre a página da disciplina, uma rejeição abre o item em Meus conteúdos;
    - o contador é consultado a cada 60 segundos e a cada troca de página;
    - quatro eventos geram notificação: material aprovado, material rejeitado, alteração aprovada e alteração rejeitada;
    - o painel mostra as 20 mais recentes.

## 4. Modelo de dados

### 4.1 Tabela `materials` (etapa 2a)

| Coluna | Tipo | Regra |
|---|---|---|
| `id` | bigint | é a base do código `MAT-%05d` |
| `discipline_id` | FK `disciplines` | `restrictOnDelete` |
| `user_id` | FK `users` | autor; `restrictOnDelete` |
| `revises_id` | FK `materials`, nulo | preenchido só em alteração (2b), apontando para o material publicado |
| `title` | `string(200)` | — |
| `description` | `string(2000)`, nulo | — |
| `url` | `string(2048)` | já normalizado (ver 5.2) |
| `anonymous` | boolean | padrão `false` |
| `status` | string | `CHECK (status IN ('PENDENTE','APROVADO','REJEITADO','CANCELADO'))` |
| `rejection_reason` | `string(500)`, nulo | `CHECK`: preenchido se e só se o status for `REJEITADO` |
| `decided_by` | FK `users`, nulo | quem aprovou ou rejeitou |
| `decided_at` | timestamp, nulo | — |
| `published_at` | timestamp, nulo | data pública, igual à da aprovação (RF-39.1) |
| `created_at` / `updated_at` | timestamps | `created_at` é a data do envio |
| `deleted_at` | timestamp, nulo | soft delete (2b), criado já na 2a para não precisar de outra migration |

**Índices:**

- `(status, created_at)`, para a fila em ordem do mais antigo;
- `(discipline_id, status, published_at)`, para a lista da disciplina;
- índice único **parcial** em `(discipline_id, url)` com `WHERE status IN ('PENDENTE','APROVADO') AND deleted_at IS NULL AND revises_id IS NULL`. Esse índice garante a decisão 3.4 no banco. **(escolha do Claude)**

O status é um enum PHP `MaterialStatus` guardado como string, no mesmo padrão de `Role`.

### 4.2 Alteração de material publicado (etapa 2b) (escolha do Claude)

Uma alteração é uma **nova linha** em `materials`, com `revises_id` apontando para o original, status `PENDENTE`, a mesma disciplina e o mesmo `anonymous`. Assim, a fila de Moderação continua sendo uma consulta só (`status = PENDENTE`).

- **Ao aprovar:** os campos `title`, `description` e `url` da alteração são copiados para o original, na mesma transação, e a alteração vira `APROVADO`. O `published_at` do original não muda.
- **Ao rejeitar:** a alteração vira `REJEITADO` com o motivo, e o original fica como estava.
- **Limite:** um material só pode ter uma alteração pendente por vez. Uma nova tentativa recebe a mensagem "Já existe uma alteração deste material em análise." (409).
- **Em Meus conteúdos,** as alterações aparecem dentro do item original, nunca como itens separados.
- **Link repetido na aprovação:** se, enquanto a alteração espera, outro envio da mesma disciplina passar a usar o mesmo link, a aprovação esbarra no índice único. Nesse caso a resposta é 409 com "O link desta alteração já foi compartilhado nesta disciplina.", e a alteração continua pendente para ser rejeitada.

### 4.3 Notificações (etapa 2c)

As notificações usam o recurso nativo do Laravel (`Notifiable` já está no `User`, canal `database`, tabela `notifications` criada por `make:notifications-table`). Cada notificação é uma classe em `app/Notifications/` (`MaterialApproved`, `MaterialRejected`, `MaterialRevisionApproved`, `MaterialRevisionRejected`). O campo `data` guarda `material_id`, `code`, `discipline_name` e `discipline_slug`. A notificação é criada na mesma transação da decisão.

## 5. Backend

### 5.1 Endpoints

Todos ficam sob `auth:sanctum`. As disciplinas são acessadas pelo **id** na API, e não pelo slug, porque o front já tem o id no catálogo em cache. Por isso não é preciso criar uma coluna de slug **(escolha do Claude)**.

| Etapa | Método e rota | Quem pode | Resposta |
|---|---|---|---|
| 2a | `GET /disciplines/{discipline}/materials?cursor=` | autenticado | os materiais `APROVADO` (sem soft delete e sem `revises_id`), por `published_at` decrescente, 20 por página (`cursorPaginate`) |
| 2a | `POST /disciplines/{discipline}/materials` | autenticado; throttle `materials` (10/h por usuário) | 201 com o material `PENDENTE` |
| 2a | `GET /moderation/materials` | Gate `access-moderation` | os pendentes, do mais antigo para o mais novo. O MODERADOR não recebe os próprios. Sem paginação (`// shortcut: fila inteira numa resposta; paginar se passar de ~200 pendentes`) **(escolha do Claude)** |
| 2a | `POST /moderation/materials/{material}/approve` | Gate + Policy | 200, 409 ou 403 |
| 2a | `POST /moderation/materials/{material}/reject` (`reason`) | Gate + Policy | 200, 409, 403 ou 422 |
| 2b | `GET /me/materials?status=` | autenticado (só os próprios) | lista de Meus conteúdos |
| 2b | `POST /materials/{material}/cancel` | autor, se `PENDENTE` | 200 ou 409 |
| 2b | `PUT /materials/{material}` | autor, se `APROVADO`; throttle `materials` | 201 com a alteração `PENDENTE` |
| 2b | `DELETE /materials/{material}` | autor, se `APROVADO` | 204 |
| 2c | `GET /notifications` | autenticado | as 20 mais recentes e `unread_count` |
| 2c | `POST /notifications/{id}/read` | dono | 204 |
| 2c | `POST /notifications/read-all` | autenticado | 204 |

**Formatos de resposta:**

- **`MaterialResource` (público):**
  - `id`, `code`, `title`, `description`, `url`, `published_at`;
  - `author_name`: o nome de exibição, ou `null` quando o material é anônimo;
  - `is_mine`: só serve para mostrar o menu ⋯ ao autor e não revela o autor a mais ninguém.
- **`ModerationMaterialResource`:**
  - os dados do material, mais `discipline` (`id`, `name`, `slug`) e `submitted_at`;
  - `author_name`: `null` para o MODERADOR quando o material é anônimo;
  - `anonymous`;
  - `is_own`, que vira a marca "Seu envio" para o ADMINISTRADOR;
  - `revision_of`: a versão publicada, quando o item é uma alteração.

### 5.2 Validação (`StoreMaterialRequest`, também usada na edição)

Os limites ficam em constantes `MAX_TITLE = 200`, `MAX_DESCRIPTION = 2000` e `MAX_URL = 2048` **(escolha do Claude: o Marcelo pediu limites maiores que 120/500, e os números são proposta minha)**.

- `prepareForValidation`:
  - aplica `trim` em tudo;
  - transforma a descrição vazia em `null`;
  - completa o link sem protocolo com `https://` (quando parece um domínio, como no protótipo).
- **Mensagens** (as do protótipo e as novas):

| Situação | Mensagem |
|---|---|
| título vazio | "Informe um título para o material." |
| link vazio | "Informe o link do material." |
| link inválido ou com protocolo diferente de http/https | "Informe um link válido." |
| link repetido | "Esse link já foi compartilhado nesta disciplina." |
| título longo | "Use no máximo 200 caracteres." |
| descrição longa | "Use no máximo 2.000 caracteres." |
| link longo | "Use no máximo 2.048 caracteres." |

- **Link repetido:** verificado no Request (para dar a mensagem no campo) e garantido pelo índice parcial. Se as duas requisições passarem pelo Request ao mesmo tempo, a violação de unicidade do banco também vira 422 com a mesma mensagem.
- **Payload malformado:** devolve 422, nunca 500.
- **429:** devolve "Muitas tentativas. Tente novamente em X segundos.", como nos outros limites.

### 5.3 Moderação (Actions `ApproveMaterial` e `RejectMaterial`)

- **Policy `MaterialPolicy::moderate`:**
  - o usuário precisa ter `canModerate()`;
  - o MODERADOR não pode decidir sobre o próprio material (403), mesmo que o acesse direto pela URL;
  - o ADMINISTRADOR pode (§51).
- **Concorrência:** a decisão é um `UPDATE ... WHERE id = ? AND status = 'PENDENTE'`. Se nenhuma linha mudar, a resposta é **409** com "Este conteúdo já foi analisado." Se o material tiver sido cancelado (2b), a resposta é 409 com "Este conteúdo foi cancelado pelo autor."
- **Rejeição:** `reason` é obrigatório (`trim`, até 500).
  - Vazio: "Informe o motivo da rejeição."
  - Longo: "Use no máximo 500 caracteres."
- **Aprovação:** preenche `published_at`, `decided_by` e `decided_at`. Uma alteração (2b) é aplicada ao original como descrito em 4.2.
- **Notificação (2c):** criada na mesma transação da decisão.

### 5.4 Termos de participação (escolha do Claude)

O texto implementado dos Termos (versão 0.1) não fala de autoria. Ele diz o que outros estudantes veem do perfil, mas não dos conteúdos. Na etapa 2a entra a **versão 0.2**, que acrescenta à seção "O que outros estudantes podem ver" o parágrafo:

> Os materiais e conteúdos que você compartilhar mostram seu nome de exibição. Se você escolher compartilhar anonimamente, os outros estudantes não verão seu nome, mas a administração do ConectaCC mantém o registro de quem enviou.

Para isso, segue-se `docs/operacao.md` ("Trocar os Termos de participação") e atualiza-se `TERMS_VERSION`. Quem já aceitou a 0.1 não precisa aceitar de novo nesta etapa, porque os Termos só são exigidos no Cadastro.

### 5.5 Seeders (escolha do Claude)

Só para dev, o seeder cria cerca de 3 materiais aprovados (um deles anônimo) e 2 pendentes. Um dos pendentes é do `moderador@` e o outro do `aluno@`. Isso permite testar a fila e a regra do próprio conteúdo sem rodar `make fresh`. Os materiais são criados por `firstOrCreate`, e o seeder pode ser rodado sozinho.

## 6. Frontend

### 6.1 Página da disciplina (2a)

- **Seção "Materiais compartilhados"** (protótipo `Disciplina`):
  - subtítulo "Mais recentes primeiro.";
  - o botão "Compartilhar material" aparece no topo da seção quando há materiais e no estado vazio quando não há;
  - estado vazio: "Ainda não há materiais compartilhados" e "Se você conhece um material que pode ajudar outros estudantes, compartilhe com a comunidade." Isso substitui o "Em breve…" da etapa 1;
  - carregando: "Carregando materiais…". Erro: "Não foi possível carregar os materiais agora." com "Tentar novamente";
  - o botão "Mostrar mais" usa `useInfiniteQuery` e some na última página.
- **Card de material:**
  - título, descrição (quando houver);
  - "Compartilhado por <nome> · <há X>" ou "Compartilhado pela comunidade · <há X>". O tempo relativo vem de `Intl.RelativeTimeFormat('pt-BR')` sobre `published_at`;
  - o link "Acessar material", que abre em nova guia, mostra o domínio ao lado e tem o sr-only "(abre em nova guia, <domínio>)".
  - Na 2b, o menu ⋯ (Editar e Excluir) aparece quando `is_mine` é verdadeiro.
- **Diálogo "Compartilhar material"** (`Dialog` do shadcn; protótipo `Disc-Form*`):
  - "Campos marcados com * são obrigatórios.";
  - Disciplina, mostrada só para leitura;
  - Título *;
  - Descrição, com a ajuda "Explique brevemente o que o material contém.";
  - Link *, com a ajuda "Use um link que outros estudantes consigam acessar.";
  - o checkbox **"Compartilhar anonimamente"**, desmarcado, com a ajuda "Outros estudantes não verão seu nome. A administração do ConectaCC continua sabendo quem enviou.";
  - o aviso "O material será analisado pela moderação antes de aparecer na disciplina.";
  - os botões "Cancelar" e "Enviar para análise" ("Enviando…" durante o envio).
- **Envio:**
  - Sucesso: "Material enviado para análise". O texto de apoio depende da etapa:
    - 2a: "O material aparecerá na disciplina depois de aprovado.";
    - 2b: "Você pode acompanhar a análise em Meus conteúdos.", com link;
    - 2c: "Você receberá uma notificação quando o material for analisado.".
  - Depois do sucesso, o botão "Voltar para a disciplina".
  - Erros 422 vão para os campos (`applyFieldErrors`). Uma falha geral mostra "Não foi possível enviar o material. Tente novamente." sem perder o que foi digitado.
- **Fechar com dados preenchidos:** abre "Descartar alterações?", com "As informações deste material ainda não foram enviadas.", "Continuar preenchendo" e "Descartar alterações". Reaproveita `DiscardChangesDialog`.

### 6.2 Moderação (`/moderacao`, 2a)

Substitui o placeholder. O guard `RequireModeration` e o item da Sidebar já existem.

- **Cabeçalho:** "Moderação" e "Analise conteúdos enviados pela comunidade antes da publicação.". Abaixo, o contador ("N conteúdos pendentes" / "1 conteúdo pendente") e "Mais antigos primeiro.".
- **Item da fila:**
  - "Tipo: Material", "Status: Pendente";
  - a etiqueta "Alteração" (2b) e a etiqueta "Seu envio" (só para o ADMINISTRADOR, no próprio envio);
  - o título;
  - "Enviado por <nome ou Comunidade> · <há X> · Disciplina: <nome>";
  - o botão "Analisar".
- **Painel "Analisar conteúdo"** (`Sheet`):
  - Autor:
    - o nome, ou "Comunidade";
    - para o ADMINISTRADOR, a marca "Enviado anonimamente" quando for o caso;
    - a nota "Visível apenas para a moderação.";
  - "Enviado em dd/mm/aaaa às hh:mm (há X)";
  - Disciplina, Descrição, "Link enviado" (domínio e caminho) e "Abrir link enviado", que abre em nova guia;
  - "Se aprovado, aparecerá em Estudos › <disciplina>";
  - os botões "Rejeitar" e "Aprovar";
  - numa alteração (2b), a versão publicada e a proposta lado a lado, como no esboço.
- **Confirmações** (`Dialog`):
  - "Aprovar conteúdo?" com o texto do protótipo e os botões "Cancelar" e "Aprovar conteúdo" ("Aprovando…" durante o envio);
  - "Rejeitar conteúdo?" com o campo "Motivo da rejeição *" (obrigatório), a ajuda "O estudante verá o motivo em Meus conteúdos." e os botões "Cancelar" e "Rejeitar conteúdo" ("Rejeitando…").
- **Resultados:**
  - aprovado: "Conteúdo aprovado com sucesso." e "O material passa a aparecer em Estudos › <disciplina>.";
  - rejeitado: "Conteúdo rejeitado.";
  - a partir da 2c, os dois avisos acrescentam "O autor será notificado.";
  - 409: "Este conteúdo já foi analisado", com "O status deste conteúdo foi atualizado por outra ação. Atualize a fila para continuar." e "Atualizar fila";
  - erro: "Não foi possível aprovar o conteúdo. Tente novamente." ou "Não foi possível rejeitar o conteúdo. Tente novamente.".
- **Estados:**
  - fila vazia: "Tudo certo por aqui" e "Não há conteúdos aguardando análise no momento.";
  - carregando: "Carregando conteúdos pendentes…";
  - erro: "Não foi possível carregar os conteúdos pendentes agora." com "Tentar novamente".
- **Sem filtro por tipo** (seção 2).

### 6.3 Meus conteúdos (`/perfil/meus-conteudos`, 2b)

Segue o esboço aprovado.

- **Acesso:** o link "Meus conteúdos" fica no cabeçalho do Perfil. A página tem o breadcrumb "Perfil / Meus conteúdos", o título "Meus conteúdos" e o texto "Acompanhe o que você enviou e a análise da moderação.".
- **Filtros:** Todos, Em andamento (PENDENTE), Aprovados, Rejeitados, Excluídos. "Cancelado" aparece só em Todos **(escolha do Claude)**. A lista vem dos mais recentes para os mais antigos.
- **Item:**
  - linha de identificação: "MAT-00042 · Material · Estudos › <disciplina>";
  - título e "Enviado em dd/mm/aaaa";
  - a etiqueta "Anônimo" quando for o caso;
  - o status: "Em análise", "Aprovado", "Rejeitado", "Cancelado" ou "Excluído";
  - "Alteração em análise desde dd/mm/aaaa", quando houver alteração pendente.
- **Ações por situação** (§52.7):

| Situação | Ações |
|---|---|
| Em análise | "Ver dados enviados" e "Cancelar envio" |
| Aprovado | "Ver conteúdo" (vai à disciplina), "Editar" e "Excluir" |
| Rejeitado | os dados enviados e o motivo, num quadro "Motivo da rejeição: …" |
| Cancelado ou Excluído | "Ver dados enviados" |

- **"Ver dados enviados"** abre os dados no próprio item **(escolha do Claude)**.
- **Lista vazia:** "Você ainda não enviou conteúdos.". Num filtro sem itens: "Nenhum conteúdo neste filtro."
- **Abrir pela URL:** `?item=MAT-00042` abre o item já expandido. A notificação de rejeição usa esse endereço.
- **Diálogos:**
  - **Cancelar envio?** Texto: "O material sai da fila de análise e não será publicado." Botões: "Voltar" e "Cancelar envio". **(escolha do Claude)**
  - **Excluir material?** Texto: "O material deixará de aparecer na disciplina. Você ainda poderá consultá-lo em Meus conteúdos." Botões: "Cancelar" e "Excluir material". **(escolha do Claude)**
  - **Editar:** usa o mesmo diálogo do envio, já preenchido, sem o checkbox de anônimo (a autoria não muda) e com o título "Editar material".
    - Sucesso: "Alteração enviada para análise", com "A versão atual continua visível até a alteração ser aprovada.".
    - Se já houver alteração pendente: "Já existe uma alteração deste material em análise.".

### 6.4 Notificações (2c)

Segue o protótipo `Home-Notificacoes`.

- **Sino no Header:**
  - o botão tem `aria-expanded` e `aria-label` "Notificações" ou "Notificações, N não lidas";
  - o badge mostra o número, ou "9+" acima de 9.
- **Painel:**
  - título "Notificações" e "Marcar todas como lidas";
  - cada item tem um ícone (✓ ou ✕), título, detalhe, tempo relativo e o selo "Nova" quando não lida;
  - Esc fecha o painel;
  - vazio: "Você ainda não tem notificações." e "Quando algo que você enviar for analisado, o resultado aparece aqui.".
- **Textos das notificações:**

| Evento | Título | Detalhe |
|---|---|---|
| material aprovado | "Seu material foi aprovado" | "O material enviado para <disciplina> foi aprovado." |
| material rejeitado | "Seu material não foi aprovado" | "O material enviado para <disciplina> foi analisado." |
| alteração aprovada (escolha do Claude) | "Sua alteração foi aprovada" | "A alteração do material em <disciplina> foi aprovada." |
| alteração rejeitada (escolha do Claude) | "Sua alteração não foi aprovada" | "A alteração enviada para <disciplina> foi analisada." |

- **Clique:** marca como lida e navega. Uma aprovação vai para `/estudos/<slug>`, e uma rejeição vai para `/perfil/meus-conteudos?item=<código>`.
- **Atualização:** `refetchInterval: 60_000` e nova consulta a cada troca de rota.

## 7. Testes

**Backend (Pest),** por etapa:

- **Validação:**
  - os 7 casos de mensagem de 5.2;
  - o link sem protocolo é completado;
  - `javascript:`, `ftp:` e um link com espaço são recusados;
  - payload malformado devolve 422.
- **Link repetido:**
  - recusado com envio pendente ou aprovado;
  - aceito depois de rejeitado, cancelado ou excluído;
  - aceito em outra disciplina.
- **Throttle:** o 11º envio na mesma hora recebe 429.
- **Lista pública:**
  - só materiais aprovados, sem excluídos e sem alterações;
  - `author_name` nulo no anônimo;
  - `is_mine` verdadeiro só para o autor;
  - paginação de 20 em 20, em ordem de `published_at` decrescente.
- **Moderação:**
  - o ALUNO recebe 403;
  - o MODERADOR não vê o próprio envio na fila e recebe 403 ao decidir sobre ele direto pela URL;
  - o MODERADOR não recebe o nome do autor de um material anônimo (o JSON não traz o nome);
  - o ADMINISTRADOR vê o nome e pode decidir sobre o próprio material;
  - a segunda decisão recebe 409 sem alterar a primeira;
  - rejeitar sem motivo recebe 422;
  - aprovar preenche `published_at`;
  - a fila vem do mais antigo para o mais novo.
- **2b:**
  - cancelar só funciona em PENDENTE e só para o autor;
  - editar cria uma alteração e não muda o original até a aprovação;
  - a alteração aprovada copia os campos para o original;
  - a alteração rejeitada mantém o original;
  - não é possível ter duas alterações pendentes;
  - excluir faz soft delete e cancela a alteração pendente;
  - outro usuário recebe 403 em todas as ações;
  - os filtros de Meus conteúdos.
- **2c:**
  - cada decisão gera exatamente uma notificação para o autor;
  - a lista traz as 20 mais recentes e o `unread_count`;
  - ler e ler todas;
  - um usuário não marca a notificação de outro.

**Frontend:** `make lint`, mais o roteiro Playwright fora do repositório em cada etapa (desktop e mobile), com os estados de cada tela e as entradas do "Foco de revisão" do plano.

## 8. Documentação a atualizar

Na etapa em que cada item entra:

- **`decisoes.md`:** a seção 55 com as decisões da seção 3. Ela diz explicitamente que substitui §18, RF-39, §37 e RF-73, e que resolve §52.5, §52.11 e §33 (comportamento do clique).
- **`requisitos.md`:**
  - nota de atualização em RF-39 (autoria e anônimo), RF-73 (motivo obrigatório) e RF-66 (MODERADOR também acessa, conforme §1.2);
- **`pendencias.md`:** 2.7, com os limites de material.
- **`implementacao.md`:** §8 etapa 2, §9 Materiais, §14 Moderação e §16 Notificações.
- **`status-mvp.md`** e o README.
- **`operacao.md`:** a troca dos Termos para 0.2.

## 9. Fica para o builder

- Os nomes de componentes e a divisão de arquivos em `features/studies/`, `features/moderation/`, `features/my-contents/` e `features/notifications/`.
- O formato visual exato das etiquetas de status, seguindo os tokens existentes.
- A forma do cursor de paginação, desde que a ordem seja estável (`published_at`, `id`).
