# Estudos (etapa 1: leitura) — design

Data: 31/10/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o merge do Perfil, PR #3)

Este documento registra o design da etapa 1 de Estudos. Ele parte de `requisitos.md` (RF-29 a RF-33), `fluxos.md` (seções 7 e 8), `implementacao.md` §8 e da spec do Perfil (`2026-10-19-perfil-design.md`), cujas convenções continuam valendo.

Referência visual: protótipo congelado (https://claude.ai/artifact/Gwv1arQoKYix47kJHF3dF4), artboards `Estudos`, `Estudos-Carregando`, `Estudos-Erro`, `Estudos-SemDisciplinas`, `Estudos-Mobile`, `Disciplina`, `Disc-SemMateriais`, `Disc-Mobile`.

## 1. Intenção

Estudos é onde o estudante chega às disciplinas que cursa e, depois, aos materiais da comunidade. Esta etapa entrega a navegação: o estudante encontra as próprias disciplinas, encontra qualquer disciplina do curso e abre a página dela com a ementa oficial.

Estudos foi dividido em duas etapas (decisão de 31/10/2026, `implementacao.md` §8):

- **Etapa 1 (este documento):** Estudos só de leitura.
- **Etapa 2:** compartilhar material, Moderação só de materiais e materiais aprovados na página da disciplina. As duas metades entram juntas na `main`, para nunca existir um envio que ninguém consegue aprovar.

## 2. Escopo

### Entra

1. **Estudos** (`/estudos`): Minhas disciplinas, Todas as disciplinas agrupadas por semestre e busca.
2. **Página da disciplina** (`/estudos/<slug>`): nome, semestre, carga horária, link da ementa oficial e a seção Materiais compartilhados ainda vazia.
3. **Slug** das disciplinas no backend, calculado a partir do nome.

### Não entra

- Formulário Compartilhar material, envio, Moderação e lista de materiais: ficam para a etapa 2.
- Estado "Materiais ainda não disponíveis" (RF-34): todas as disciplinas terão área de materiais (decisão 3.1), então esse estado não aparece. O RF-34 continua válido caso alguma disciplina precise ser desligada no futuro, e o campo para isso só será criado quando houver essa necessidade.
- Eletivas e Matriz 2017: continuam fora do catálogo.

## 3. Decisões do Marcelo (31/10/2026)

1. **Todas as 49 disciplinas têm área de materiais.** Não existe lista de disciplinas piloto para materiais. Isso substitui a pendência de `pendencias.md` 1.3 e de `decisoes.md` §19 ("Disciplinas piloto") na parte de materiais.
2. **Todas as disciplinas agrupadas por semestre**, do 1º ao 8º, em ordem alfabética dentro de cada grupo.
3. **A página da disciplina mostra semestre e carga horária** junto do link da ementa oficial.
4. **Endereço legível:** `/estudos/estrutura-de-dados-iii`, e não `/estudos/12`.

## 4. Backend

### 4.1 Slug

- O slug é **calculado a partir do nome** (`Str::slug($name)`) por um accessor do model `Discipline`. Ele não fica guardado no banco. Uma coluna nova quebraria o `migrate:fresh`: a migration de carga do catálogo (`2026_10_19_000003`) roda antes dela e chama `DisciplineCatalog::sync`. Calcular evita migration e dá o mesmo endereço.
- Os 49 nomes atuais geram 49 slugs distintos (verificado), e um teste garante isso. Se um nome futuro colidir, o teste falha.
- O slug é derivado do nome. Se o nome de uma disciplina mudar, o endereço muda junto. Hoje os nomes vêm da Matriz oficial e não mudam.
- Na etapa 2, se o backend precisar buscar disciplina por slug (`/disciplines/{slug}/materials`), avaliar ali se vale criar a coluna. Isso exige uma migration que não use `DisciplineCatalog::sync`.

### 4.2 API

**Não há endpoint novo.** `GET /api/v1/disciplines` já devolve o catálogo inteiro, ordenado por semestre e nome, e o front o guarda em cache com `staleTime: Infinity`. São 49 linhas. `DisciplineResource` passa a devolver também `slug`, `workload_hours` e `syllabus_url`. O Perfil reaproveita o mesmo resource e só ganha campos a mais.

A página da disciplina encontra a disciplina pelo slug nessa lista. A etapa 2 adiciona `GET /api/v1/disciplines/{slug}/materials`.

Depois da mudança, rodar `make api-types` para regenerar `frontend/openapi.json` e os tipos.

## 5. Frontend

Nova pasta `frontend/src/features/studies/`. As rotas `/estudos` e `/estudos/:slug` substituem o placeholder "Em construção" em `router.tsx`. O item Estudos da Sidebar continua ativo dentro de `/estudos/<slug>`.

Os dados vêm dos hooks que já existem: `useDisciplines()` para o catálogo e `useProfile()` para as disciplinas atuais (`profile.disciplines`). Nenhum dos dois é duplicado.

### 5.1 Estudos (`/estudos`)

Cabeçalho: "Estudos", com o subtítulo "Acesse suas disciplinas e materiais compartilhados pela comunidade." (texto do protótipo).

**Minhas disciplinas**
- Mostra os cards das disciplinas de `profile.disciplines`, na ordem do catálogo (semestre, depois nome), com o link "Alterar no Perfil" (`/perfil?editar=disciplinas`).
- Sem disciplinas: "Você ainda não selecionou suas disciplinas", o texto "Adicione as disciplinas que está cursando para encontrá-las rapidamente aqui." e o botão "Completar perfil" (`/perfil?editar=disciplinas`).
- A busca não filtra esta seção, como no protótipo.

**Todas as disciplinas**
- O campo "Buscar disciplina" filtra pelo nome sem diferenciar maiúsculas de minúsculas e ignorando acentos ("computacao" encontra "Computação"). A busca roda no navegador.
- Os grupos aparecem como "1º semestre" a "8º semestre", e um grupo sem resultados some.
- Cada semestre abre e fecha ao clicar no título (botão com `aria-expanded`). Todos começam fechados (decisão do Marcelo, 31/10/2026). Durante a busca, os semestres com resultado abrem sozinhos e voltam a fechar quando a busca é limpa.
- O contador mostra "N disciplinas" ou "1 disciplina" e é anunciado ao leitor de tela (`aria-live="polite"`).
- Sem resultados: "Nenhuma disciplina encontrada para “<busca>”."
- O aviso do protótipo "Lista de demonstração do protótipo…" não entra, porque o catálogo agora é o oficial.

**Card de disciplina** (o mesmo nas duas seções)
- Nome da disciplina, o selo "Minha disciplina" no canto superior direito (decisão do Marcelo) quando ela está em `profile.disciplines` e a ação "Ver disciplina", que leva a `/estudos/<slug>`. O card inteiro é um único link.
- **A linha de status do protótipo ("Materiais disponíveis") não entra.** Como toda disciplina tem área de materiais, a linha seria igual em todos os cards e não informaria nada. A etapa 2 pode trocá-la pela quantidade de materiais.

**Estados**
- Carregando: skeletons como em `Estudos-Carregando`, com o componente `Skeleton` existente.
- Erro (catálogo ou perfil): "Não foi possível carregar as disciplinas agora." e o botão "Tentar novamente", que refaz as duas consultas que falharam.

**Layout:** 3 colunas no desktop e 1 coluna no mobile, como no protótipo.

### 5.2 Página da disciplina (`/estudos/:slug`)

- Breadcrumb "Estudos / <nome>", com "Estudos" como link.
- Título com o nome da disciplina e o selo "Minha disciplina" quando for o caso.
- Card **Informações da disciplina**: "<N>º semestre · <H> h", o texto "Consulte a ementa e as informações oficiais na fonte institucional." e o link "Ver ementa oficial", que abre em nova guia com indicação para leitor de tela (padrão `srTxt` do protótipo: "(abre em nova guia, inf.passofundo.ifsul.edu.br)"). Se `syllabus_url` vier nulo, aparece o texto "Link oficial a confirmar" sem link (RF-33). Hoje as 49 disciplinas têm link.
- Seção **Materiais compartilhados**, ainda sem materiais: o título da seção, "Ainda não há materiais compartilhados" e o texto "Em breve você poderá compartilhar materiais nesta disciplina." Não há botão. A etapa 2 troca esse texto pelo do protótipo e adiciona "Compartilhar material".
- Slug inexistente: "Disciplina não encontrada", com o link "Voltar para Estudos". A página não redireciona.
- Carregando e erro seguem os mesmos padrões de 5.1.

### 5.3 Acessibilidade

- Cada seção tem um título real (`h2`) e cada grupo de semestre tem um `h3`.
- O campo de busca tem `label` visível.
- O link externo da ementa tem o texto de nova guia descrito acima.
- O foco e a navegação por teclado seguem os padrões já usados no Perfil e na Home.

## 6. Testes

- **Backend (Pest):**
  - as 49 disciplinas têm slugs únicos (por exemplo, "Criação de Páginas WEB" vira `criacao-de-paginas-web`);
  - `GET /disciplines` devolve `slug`, `workload_hours` e `syllabus_url`;
  - a rota continua exigindo autenticação.
- **Frontend:** `npm run lint` e `npm run typecheck` (`make lint`). O front não tem runner de testes, e a normalização da busca é uma linha (`normalize('NFD')` sem os diacríticos, em minúsculas). Ela é conferida à mão na tela, com "computacao" encontrando "Computação".

## 7. Documentação a atualizar junto com o código

- `decisoes.md`: as decisões da seção 3.
- `pendencias.md` 1.3: as disciplinas piloto para materiais deixam de estar pendentes (todas as disciplinas).
- `implementacao.md` §8: marcar os itens da etapa 1.
- `status-mvp.md`: Estudos passa a "implementado (leitura)".
- `README.md`, "Estado da implementação": incluir Estudos (leitura).

## 8. Fica para o builder

- Os nomes de arquivos e componentes dentro de `features/studies/`.
- Como dividir os componentes entre a página Estudos e a página da disciplina, reaproveitando `Chip`, `Skeleton`, `FormAlert`, `Button` e `TextField`.
- Os espaçamentos finos, seguindo o protótipo e as páginas já implementadas.
