# Oportunidades — design

Data: 08/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #16)

Referência visual: protótipo, artboards `Oportunidade` e `Op-*`. O cabeçalho segue a faixa da Comunidade (`decisoes.md` 68.8). Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Estudantes encontram vagas, estágios e eventos espalhados por grupos e redes. Oportunidade reúne essas indicações num lugar do curso, moderadas, e aponta para a fonte original. O ConectaCC é **centralizador e ponte**: guarda só os metadados e o link, nunca uma cópia da página externa, e não faz raspagem (`decisoes.md` 53.1).

É uma funcionalidade adicional, como a Comunidade (§56.3), e já entra integrada ao mural (§68.6 e §69).

## 2. Escopo

### Entra

- Página `/oportunidade` com o card das bolsas do IFSul e as oportunidades aprovadas, com busca e filtro por tipo.
- Envio pelo botão "+ Adicionar oportunidade", com Moderação.
- Prazo: o campo "Inscrições até", ou 15 dias quando ele fica vazio.
- Notificações, Meus conteúdos (cancelar e excluir), auditoria e uma página só de leitura no `/admin`.
- Mural da Comunidade: as oportunidades aprovadas, o filtro por tipo, o menu "Compartilhar" e o número de oportunidades.

### Não entra

- **Raspagem, importação ou cópia de páginas externas.**
- **Edição de oportunidade:** para corrigir, a pessoa exclui e envia de novo.
- **Envio anônimo:** a autoria nunca é pública, então a opção não faz sentido.
- **"Explorar por tipo":** fica para depois (§69.3).
- **Escolher a disciplina de um material dentro da Comunidade:** o menu "Compartilhar" leva a Estudos, como hoje.

## 3. Decisões (08/11/2026)

1. **Tipos:** os cinco do RF-43 e da §22, sem criação livre: Estágio, Emprego, Evento, Projeto e Outro.
2. **Campos:** Tipo, Título (até 200), Descrição curta (obrigatória, até 300), Link e Modalidade (Remoto, Híbrido ou Presencial), todos obrigatórios. O link segue a validação do material e recusa um link já ativo em outra oportunidade. Isso substitui a "Descrição opcional" do RF-42.
3. **Prazo:** "Inscrições até" é opcional. Vazio, vale um prazo padrão de 15 dias. Depois do prazo, a oportunidade sai da página e do mural, sem ação de ninguém.
4. **Autoria:** o sistema registra quem enviou. MODERADOR e ADMINISTRADOR veem essa pessoa na análise e no `/admin`. **Nenhuma área pública mostra autoria**: nem o nome, nem "Publicado por Comunidade". O MODERADOR não analisa a própria oportunidade, como no material.
5. **Autor:** vê as próprias oportunidades em Meus conteúdos, com "Cancelar envio" enquanto pendente e "Excluir" depois de aprovada. Sem edição.
6. **Página:** "Oportunidade" no singular na navegação (§6), entre Estudos e Comunidade. No topo, o card fixo das bolsas do IFSul. Depois, a busca, o filtro por tipo e a lista.
7. **Bolsas do IFSul:** link confirmado pelo time, `https://www.ifsul.edu.br/estudante-pronatec`. O card só redireciona; nada é cadastrado no banco.
8. **Mural:** "Comunidade adicionou uma nova oportunidade em Estágio: [Título]", que leva à oportunidade já rolada e destacada. Entram o filtro por tipo (§69.1) e o número de oportunidades.
9. **"Compartilhar" na Comunidade:** um menu com "Material", que leva a Estudos, e "Oportunidade", que abre o mesmo modal da página.
10. **Moderação:** a fila mostra os dois tipos juntos, com a etiqueta "Material" ou "Oportunidade", sem filtro. Aprovar e rejeitar com motivo seguem as mesmas regras do material.
11. **Entrega única**, com tudo acima.

## 4. Backend

### Tabela `opportunities`

As colunas seguem `materials`:

- `user_id`, `type`, `title`, `description`, `url`, `modality` e `deadline` (data, opcional);
- `status`, `rejection_reason`, `decided_by`, `decided_at` e `published_at`;
- `timestamps` e `softDeletes`.

**Regras no banco:**

- `CHECK` em `type`, `modality` e `status` (sem estado de alteração);
- motivo obrigatório só no REJEITADO, como em `materials`;
- link único entre as oportunidades pendentes e aprovadas que não foram excluídas.

**Prazo efetivo:** `deadline`, ou `published_at` mais 15 dias quando o campo está vazio. Os 15 dias contam a partir da aprovação, porque só a partir daí a oportunidade aparece **(escolha do Claude)**.

A oportunidade fica visível até o fim do dia do prazo, no horário de Brasília. No envio, "Inscrições até" não pode ser uma data passada.

### Rotas

| Rota | Quem | O que faz |
|---|---|---|
| `GET /opportunities?q=&type=&cursor=` | logado | aprovadas, não excluídas e dentro do prazo. Mais recentes primeiro, 20 por vez. Busca sem acentos no título e na descrição |
| `POST /opportunities` | logado | envia como PENDENTE, com o mesmo limite de envios por hora do material |
| `POST /opportunities/{id}/cancel` | autor | cancela o envio pendente |
| `DELETE /opportunities/{id}` | autor | exclui a aprovada |
| `GET /me/opportunities?filtro=&cursor=` | logado | as próprias, com as situações de Meus conteúdos |
| `GET /moderation/opportunities` | MODERADOR e ADMINISTRADOR | pendentes, sem as do próprio MODERADOR, com o autor |
| `POST /moderation/opportunities/{id}/approve` e `/reject` | MODERADOR e ADMINISTRADOR | mesmas regras e conflitos do material |

**O que cada resposta mostra:**

- **Resposta pública:** nunca traz autor nem `user_id`. Só traz `deadline` quando o campo foi preenchido.
- **Resposta da Moderação:** traz o nome do autor.

### Mural

`GET /community/feed` passa a misturar materiais e oportunidades em uma lista só, ordenada pela publicação e paginada por cursor.

- **Novo parâmetro `type`:** `material` ou `opportunity`.
- **Campo `kind`:** cada item ganha `kind` e os campos do seu tipo. A oportunidade nunca traz autor.
- **Oportunidade vencida ou excluída:** sai do mural.
- **Busca:** inclui o título e a descrição das oportunidades.

`GET /community/stats` ganha `opportunities`, com as oportunidades visíveis.

### Notificações, auditoria e `/admin`

- **Notificação de decisão:** como a do material. A aprovada leva a `/oportunidade#oportunidade-<id>`, e a rejeitada leva ao item em Meus conteúdos.
- **Auditoria (regra §66.7):** a tabela ganha `opportunity_id` e os eventos "Oportunidade enviada", "Envio cancelado", "Oportunidade excluída", "Aprovada" e "Rejeitada". O título fica no evento, como no material.
- **`/admin` → Oportunidades:** só de leitura, com autor, tipo, situação e decisão, nos moldes de Moderações.

Mudou a API: rodar `make api-types`.

## 5. Frontend

### Página `/oportunidade`

- **Cabeçalho:** "Oportunidade" e "Encontre oportunidades para complementar sua formação acadêmica e profissional. Aqui você encontra recursos do IFSul e indicações de estudantes aprovadas pela moderação.", na faixa com ícone de maleta.
- **Card das bolsas:**
  - "Conheça as oportunidades de bolsas do IFSul", com a marca "Fonte oficial · IFSul";
  - "Consulte bolsas, programas e oportunidades disponibilizadas oficialmente pelo IFSul.";
  - "Ver oportunidades ↗", que abre em nova aba.
- **Seção das indicações:** "Oportunidades indicadas pela comunidade" e "Veja oportunidades compartilhadas por estudantes e aprovadas pela moderação do ConectaCC.", com o botão "+ Adicionar oportunidade".
- **Busca e filtro:** "Buscar oportunidades" e um filtro por tipo ("Todos os tipos" e os cinco). Ambos ficam no endereço da página.
- **Card:**
  - etiqueta do tipo, título, modalidade e descrição;
  - "Inscrições até dd/mm/aaaa", só quando o campo foi preenchido;
  - "Publicada há X", sem autoria;
  - "Acessar oportunidade ↗" e o domínio do link.

  Os cards ficam em grade de duas colunas a partir de 768 px.
- **Estados:**
  - **Carregando:** "Carregando oportunidades…".
  - **Erro:** "Não foi possível carregar as oportunidades agora." com "Tentar novamente".
  - **Vazio:** "Nenhuma oportunidade compartilhada por enquanto" e "Conhece uma oportunidade que pode ajudar outros estudantes? Compartilhe com a comunidade.".
  - **Busca sem resultado:** "Nenhuma oportunidade encontrada", "Tente buscar por outro termo." e "Limpar busca e filtro".

### Modal "Adicionar oportunidade"

- **Campos:** Tipo, Título, Descrição curta (com contador de 300), Link, Modalidade e Inscrições até.
- **Ajuda de "Inscrições até":** "Opcional. Sem data, a oportunidade fica visível por 15 dias após a aprovação.".
- **Aviso:** "A oportunidade será analisada pela moderação antes de aparecer na página.".
- **Botões:** "Cancelar" e "Enviar para análise" ("Enviando…" durante o envio).
- **Sucesso:** "Oportunidade enviada para análise" e "Você receberá uma notificação quando sua indicação for analisada.".
- **Sair com dados preenchidos:** "Descartar alterações?", com a mesma guarda dos outros formulários.

### Outras telas

- **Sidebar:** Home, Estudos, Oportunidade, Comunidade e Perfil.
- **Moderação:**
  - a fila mistura materiais e oportunidades por data de envio, com a etiqueta do tipo;
  - o painel de análise da oportunidade mostra tipo, título, descrição, modalidade, prazo, link e quem enviou;
  - "Aprovar" e "Rejeitar" funcionam como no material.
- **Meus conteúdos:** um filtro "Tipo" com "Materiais" (padrão) e "Oportunidades", junto dos filtros de situação **(escolha do Claude: as duas listas têm paginação separada, e juntá-las numa só seria bem mais complexo)**.
- **Notificações:** os textos das notificações de material, trocando "material" por "oportunidade".
- **Comunidade:**
  - o card da oportunidade usa o ícone de maleta e a etiqueta "Oportunidade";
  - o filtro "Tipo" fica ao lado da ordem;
  - "Compartilhar" vira um menu com "Material" e "Oportunidade";
  - "A comunidade em números" ganha "Oportunidades".

## 6. Testes

### Backend (Pest)

- **Envio:** validações e mensagens de cada campo, link repetido, prazo passado e limite por hora.
- **Página:** só aparecem as aprovadas, não excluídas e dentro do prazo, inclusive o padrão de 15 dias e o último dia no horário de Brasília. A busca ignora acentos, o filtro por tipo funciona, e a paginação não repete itens.
- **Autoria:** nenhuma resposta pública traz o autor. A Moderação traz.
- **Moderação:** o MODERADOR não vê a própria oportunidade. Uma decisão tardia recebe 409, e a rejeição exige motivo. A notificação vai para o autor.
- **Autor:** cancelar e excluir só o autor e só na situação certa. Meus conteúdos mostra as próprias oportunidades.
- **Auditoria:** um evento por ação, e nenhum em ação recusada.
- **Mural:** mistura os dois tipos na ordem certa, o filtro por tipo funciona, e a oportunidade vencida ou excluída sai. Os números contam as oportunidades visíveis.
- **`/admin`:** só leitura, com o autor.

### Navegador (Playwright fora do repositório)

- Enviar, aprovar e ver a oportunidade na página e no mural, inclusive o link que rola até ela.
- Rejeitar e ver a notificação e Meus conteúdos.
- Menu "Compartilhar", filtros, estados e larguras de 1440, 390 e 320 px.

## 7. Documentação a atualizar

- **`decisoes.md`:** nova seção com as decisões da seção 3.
- **`requisitos.md`:** RF-40 a RF-45, com nota de substituição no RF-42 (descrição obrigatória, modalidade e prazo).
- **`mvp.md` e `fluxos.md`:** nota de que Oportunidade voltou como funcionalidade adicional.
- **Demais documentos:** `pendencias.md` 1.8 (resolvida em parte), `status-mvp.md`, `implementacao.md`, `roadmap.md`, `README.md` e o `AGENTS.md` da raiz.

## 8. Fica para o builder

- Se as Actions e a policy da oportunidade reaproveitam código do material por herança, por um trait ou só copiando o padrão.
- Como montar o mural misturando os dois tipos (`UNION` com cursor ou outra forma), desde que a paginação não repita nem pule itens.
- O formato do campo de data e da modalidade no modal.

## 9. Correções feitas na implementação (08/11/2026)

- **Nomes dos eventos de auditoria:** "Oportunidade enviada", "Oportunidade cancelada", "Oportunidade excluída", "Oportunidade aprovada" e "Oportunidade rejeitada". Os rótulos "Envio cancelado", "Aprovada" e "Rejeitada" ficariam iguais ou parecidos com os do material no filtro da Auditoria.
- **Botão da Comunidade:** "Compartilhar conteúdo", com um menu ("Material" e "Oportunidade") **(escolha do Claude)**.
- **"A comunidade em números":** virou uma lista, porque "Oportunidades" não cabia em três blocos lado a lado na coluna lateral.
- **Situação da oportunidade nas tags:** no feminino, por exemplo "Aprovada", "Rejeitada" e "Encerrada".
- **Visual da página, a partir de uma imagem de referência do time:**
  - o título passou a ser "Oportunidades", no plural, como a §6 permite no conteúdo da página (o item da navegação continua "Oportunidade");
  - o card das bolsas do IFSul fica dentro do cabeçalho, ao lado do texto, com o botão "Ver oportunidades";
  - o cabeçalho ganhou uma onda decorativa;
  - o estado vazio passou a ser "Nenhuma oportunidade compartilhada por enquanto" e "Quando novas oportunidades forem aprovadas, elas aparecerão aqui.", sem botão, porque "+ Adicionar oportunidade" já fica logo acima.
- **Tipos:** a imagem mostrava botões no estilo de abas ("Todas", "Vagas e Estágios", "Hackathons e Eventos"). A pedido do time, os tipos ficam num filtro de seleção ao lado da busca, com os cinco tipos (§69.1 e §70.2).
- **Logo do IFSul:** o card usa um ícone, e não o logo da imagem. O logo só entra com o arquivo oficial **(escolha do Claude)**.

