# Comunidade (mural de materiais) — design

Data: 08/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #15)

Referência visual: protótipo, artboards `Comunidade` e `Com-*`. Só o visual do mural ("O que está rolando") é usado; canais, busca de estudantes e "Compartilhar com a comunidade" ficam de fora. Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Hoje um material aprovado só é encontrado por quem abre a disciplina certa. A Comunidade volta como uma **camada de descoberta**: um mural cronológico do que foi compartilhado no curso, sem guardar cópia de nada. Estudos é dono do material, e o mural só aponta para ele.

É a retomada prevista na §56.3: funcionalidade adicional, fora do escopo obrigatório do MVP. A próxima entrega é Oportunidades, que já entra integrada a este mural.

## 2. Escopo

### Entra

- Página `/comunidade` com o mural, para todos os papéis.
- Uma publicação por material aprovado, com o autor (ou "Comunidade"), o título e a disciplina.
- Item "Comunidade" na sidebar, entre Estudos e Perfil.

### Não entra

- **Oportunidades e os filtros "Tudo", "Materiais" e "Oportunidades":** entram na entrega de Oportunidades. Com só um tipo, os filtros mostrariam a mesma lista.
- **Tópicos, respostas, comentários, curtidas e posts de texto livre:** fora desta versão.
- **Busca de estudantes e canais da Comunidade:** continuam fora (§56).
- **Atalho na Home:** a Home não muda.

## 3. Decisões (08/11/2026)

1. **Só materiais agora.** Oportunidades vem na próxima entrega, já integrada ao mural, junto com os filtros.
2. **O que gera uma publicação:** só a primeira aprovação do material. Uma alteração aprovada não gera nova publicação, e o mural mostra o título atual. Material excluído sai do mural.
3. **Ordem:** mais recentes primeiro, pela data de publicação, com o tempo relativo ("há 2 horas"). O mural carrega 20 por vez, com "Mostrar mais".
4. **Título do material:** abre a página da disciplina já rolada até o material, com um destaque rápido. O nome da disciplina abre a disciplina.
5. **Autoria:** material identificado mostra o nome, que leva ao Perfil público (ou a `/perfil`, quando é a própria pessoa). Material anônimo mostra "Comunidade", sem link de perfil, para todo mundo, inclusive para o próprio autor e o ADMINISTRADOR. A API não envia o autor (§55.2 e §57.2).
6. **Navegação:** "Comunidade" volta à sidebar, e `/comunidade` abre o mural em vez de levar à Home.
7. **Visual:** o do mural do protótipo, só a lista e os cards.

## 4. Backend

### Rota

**`GET /api/v1/community/feed`**, dentro de `auth:sanctum`. Paginação por cursor, 20 por página **(escolha do Claude)**: a lista muda enquanto a pessoa lê, e o cursor não repete nem pula itens quando um material novo é aprovado.

Consulta direta à tabela `materials`, sem tabela nova:

- `status = APROVADO`;
- não excluído;
- `revises_id` nulo, porque a alteração não é uma publicação;
- ordenado por `published_at` e `id`, decrescentes.

Cada item:

| Campo | Conteúdo |
|---|---|
| `id`, `code`, `title`, `url`, `published_at` | do material |
| `discipline` | `name` e `slug` |
| `author_name`, `author_code` | nulos no material anônimo |
| `is_mine` | `true` só quando o material é identificado e é da própria pessoa |

O mesmo critério de anonimato do `MaterialResource`: ninguém recebe o autor de um material anônimo pela área pública. Mudou a API: rodar `make api-types`.

## 5. Frontend

Pasta `features/community/`, com a página e o hook do mural.

### Textos

- **Cabeçalho:** "Comunidade" e "Veja o que a comunidade de Ciência da Computação tem compartilhado." **(escolha do Claude: o subtítulo do protótipo falava em encontrar pessoas, o que não entra)**.
- **Seção:** "O que está rolando" e "Mais recentes primeiro.".
- **Publicação identificada:** "**Marcos** compartilhou **“Resumo de Grafos”** em **Estrutura de Dados II**", com o tempo relativo ao lado.
- **Publicação anônima:** "**Comunidade** compartilhou **“Resumo de Grafos”** em **Estrutura de Dados II**".
- **Mais itens:** botão "Mostrar mais".
- **Vazio:** "Ainda não há novidades compartilhadas" e "Quando um material for aprovado em Estudos, ele aparece aqui.", com o link "Ir para Estudos" **(escolha do Claude: o protótipo oferecia publicar, o que não entra)**.
- **Carregando:** três blocos cinzentos e, para leitores de tela, "Carregando novidades…".
- **Erro:** "Não foi possível carregar as novidades agora." com "Tentar novamente". Se o erro for em "Mostrar mais", os itens já carregados continuam na tela.

### Card

- Avatar de 40 px:
  - **Material identificado:** iniciais do nome de exibição, como no `UserAvatar`.
  - **Material anônimo:** um ícone neutro de pessoas.
- **Links:**
  - **Nome:** Perfil público, ou `/perfil` quando é a própria pessoa.
  - **Título:** `/estudos/<slug>#material-<id>`.
  - **Disciplina:** `/estudos/<slug>`.
- **Visual:** borda azul-clara, cantos arredondados e fundo branco, como os cards de material de Estudos.

### Ir até o material

A página da disciplina, ao abrir com `#material-<id>`, rola até o card do material depois que a lista carrega e o destaca por cerca de 2 segundos.

Se o material não estiver mais na lista (excluído entre o mural e o clique), a página abre normalmente, sem erro.

## 6. Testes

### Backend (Pest)

- Lista só materiais aprovados, não excluídos e originais. Pendentes, rejeitados, cancelados, excluídos e alterações ficam de fora.
- Ordena por publicação, e a paginação por cursor não repete itens.
- Material anônimo não envia o nome nem o código do autor, para ALUNO, MODERADOR e ADMINISTRADOR.
- `is_mine` só no material identificado da própria pessoa.
- Uma alteração aprovada mostra o título novo, sem gerar outra publicação.
- Sem sessão: 401.

### Navegador (Playwright fora do repositório)

- Material identificado e anônimo no mural, com os links certos e sem link de perfil no anônimo.
- O título leva à disciplina rolada até o material, com destaque.
- "Mostrar mais", estado vazio, carregamento e erro.
- Sidebar com "Comunidade" e `/comunidade` abrindo o mural.
- 1440, 390 e 320 px, sem rolagem horizontal.

## 7. Documentação a atualizar

- `decisoes.md`: nova seção com as decisões da seção 3, registrando a retomada prevista na §56.3.
- `requisitos.md`, `mvp.md` e `fluxos.md`: nota de que a Comunidade voltou como mural de descoberta, sem publicações próprias.
- `status-mvp.md`, `implementacao.md`, `roadmap.md` e `README.md`.
- `frontend/AGENTS.md`, se citar a Comunidade fora do MVP.

## 8. Fica para o builder

- O nome do Resource do mural, ou se o `MaterialResource` ganha a disciplina.
- Como o card trata títulos e nomes longos no celular.
- O ícone do avatar anônimo.
