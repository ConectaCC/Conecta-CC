# Catálogo de disciplinas, Perfil e card "Complete seu perfil" — design

Data: 19/10/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o merge da fundação, PR #2)

Este documento registra o design combinado para a segunda entrega de código do ConectaCC. Ele parte das decisões de `decisoes.md`, `requisitos.md` (RF-18 a RF-25), `fluxos.md` (seções 4, 5 e 7) e da spec da fundação (`2026-10-12-fundacao-design.md`), cujas convenções continuam valendo.

Referência visual (os números de tela citados nesta spec são as páginas destes PDFs, que ficam fora do repositório):

- Perfil: `~/Downloads/ConectaCC — Protótipo de telas.pdf`, 11 páginas. 1: visualização vazia (desktop); 2: visualização preenchida; 3: visualização vazia (mobile); 4: edição (mobile, lista de disciplinas aberta); 5: edição vazia; 6: edição preenchida com a lista de disciplinas aberta; 7: erro de validação; 8: salvando; 9: sucesso; 10: falha ao salvar; 11: "Descartar alterações?".
- Home: `~/Downloads/ConectaCC — Protótipo de telas-1.pdf`, página 1 (primeiro acesso com o card "Complete seu perfil").

## 1. Intenção

O Perfil é a primeira peça de conteúdo do próprio estudante. Ele resolve duas coisas:

- o estudante diz quem é e o que cursa agora (disciplinas atuais), o que depois alimenta Estudos ("Minhas disciplinas"), a Comunidade (busca por disciplina, apoio entre estudantes) e o Perfil público;
- a Home deixa de ser igual para todos: enquanto o Perfil estiver incompleto, ela convida a completá-lo.

O catálogo de disciplinas entra agora porque o Perfil depende dele, e ele já é a base do que vem em Estudos.

## 2. Escopo

### Entra

1. **Catálogo de disciplinas** com as disciplinas obrigatórias da Matriz 2023 do BCC do IFSul – Câmpus Passo Fundo, do 1º ao 8º semestre.
2. **Perfil** (`/perfil`): visualização e edição do próprio Perfil, como no protótipo, exceto o Semestre.
3. **Card "Complete seu perfil"** na Home.

### Não entra

- Campo **Semestre** (decisão do Marcelo nesta conversa). Some do card Identidade, da edição e do subtítulo do cabeçalho do Perfil.
- **Sugestões** de tags ("Sugestões: + Git…"). Ficam para o futuro.
- **Eletivas** da Matriz 2023 (15 disciplinas). Ficam fora do catálogo por enquanto.
- **Matriz 2017.** Será verificada depois e não bloqueia esta entrega.
- Escolha das **disciplinas piloto** e da área de materiais. Não bloqueia o catálogo.
- **Estudos** ("Minhas disciplinas", "Todas as disciplinas", página da disciplina). Continua "Em construção".
- **Perfil público** (o Perfil visto por outros estudantes) e busca de estudantes.
- **Limites de texto definitivos** (`pendencias.md` 2.7). Continuam pendentes; ver seção 5.4.

## 3. Princípio: referência, não cópia

O ConectaCC guarda a **referência oficial** de cada disciplina (o link do PDF da ementa no site do IFSul), e não uma cópia do PDF nem do conteúdo institucional. O site do IFSul continua sendo a fonte canônica. Quando a página da disciplina existir, ela mostra "Ver ementa oficial ↗".

Esse princípio vale para integrações futuras (Moodle, SUAP, páginas institucionais): o ConectaCC armazena referências, metadados e vínculos necessários para organizar e relacionar a informação, sem duplicar o que já existe em outro sistema. Registrar como nova decisão em `decisoes.md`.

## 4. Catálogo de disciplinas

### Fonte

Grade Curricular Matriz 2023: https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html (abas "1º Semestre" a "8º Semestre"; a aba "Eletivas" e o bloco da Matriz 2017 ficam de fora).

São **49 disciplinas obrigatórias**:

| Semestre | Disciplinas |
|---|---|
| 1º | Algoritmos I; Introdução à História e Conceitos da Computação; Matemática Discreta; Circuitos Digitais; Criação de Páginas WEB; Leitura e Produção Textual Acadêmica |
| 2º | Algoritmos II; Sociedade e Tecnologia; Arquitetura de Computadores I; Cálculo Diferencial e Integral I; Fundamentos de Eletricidade e Eletrônica; Comunicação em Língua Inglesa |
| 3º | Estrutura de Dados I; Arquitetura de Computadores II; Cálculo Diferencial e Integral II; Tecnologia de Orientação a Objetos; Banco de Dados I; Leitura em Língua Inglesa; Práticas Curriculares Sociedade I |
| 4º | Estrutura de Dados II; Linguagem de Programação Orientada a Objetos; Redes de Computadores I; Sistemas Operacionais I; Banco de Dados II; Análise e Projeto de Sistemas; Práticas Curriculares Sociedade II |
| 5º | Estrutura de Dados III; Programação para WEB; Redes de Computadores II; Sistemas Operacionais II; Teoria da Computação; Álgebra Linear e Geometria Analítica; Práticas Curriculares Sociedade III |
| 6º | Inteligência Artificial; Sistemas Distribuídos I; Segurança em Redes de Computadores; Análise e Complexidade de Algoritmos; Metodologia da Pesquisa; Computação Gráfica; Práticas Curriculares Sociedade IV |
| 7º | Sistemas Distribuídos II; Engenharia de Software; Compiladores; Probabilidade e Estatística; Trabalho de Conclusão I; Serviços Web; Práticas Curriculares Sociedade V |
| 8º | Gestão Empresarial e Empreendedorismo; Trabalho de Conclusão II |

### Regras de nome

- Os nomes seguem o site, inclusive onde diferem do protótipo ("Segurança em Redes de Computadores", "Práticas Curriculares Sociedade III").
- Única correção: "Gestão Empresarial e Empre**e**ndedorismo" (o site traz "Empreededorismo").
- Espaços extras e quebras de linha do HTML são removidos.

### Dados de cada disciplina

| Campo | Conteúdo |
|---|---|
| nome | como na tabela acima, único |
| semestre | 1 a 8 |
| carga horária | em horas, como no site (ex.: 75) |
| link da ementa | URL absoluta do PDF no site do IFSul, com caracteres especiais codificados (alguns nomes de arquivo têm espaço) |

Nesta entrega, o Perfil só usa nome e semestre (para ordenar). Carga horária e ementa ficam guardadas para Estudos.

### Como o catálogo chega ao banco

- O conteúdo fica num arquivo versionado no backend (`backend/resources/catalog/matriz-2023.json`), no mesmo espírito dos Termos.
- Uma **migration de dados** grava o arquivo no banco com upsert pelo nome. Assim o catálogo chega à produção sozinho (o container roda as migrations na inicialização) e não depende do seeder de desenvolvimento, que cria contas de teste.
- Mudanças futuras no catálogo entram por uma nova migration.
- Os 49 links de ementa foram conferidos em 19/10/2026 e todos responderam 200. A coluna do link é opcional mesmo assim: uma disciplina futura sem ementa confirmada entra sem link, e nunca se inventa uma URL institucional (`decisoes.md` §20).

## 5. Perfil

### 5.1 Campos

| Campo | Obrigatório | Regras |
|---|---|---|
| Nome de exibição | sim | texto; espaços nas pontas removidos; máximo de 120 caracteres (o mesmo limite que o Cadastro e a coluna já usam) |
| Curso | — | fixo "Ciência da Computação" (RF-21); não é editável nem é guardado por usuário |
| Disciplinas atuais | sim, pelo menos uma | só disciplinas do catálogo; sem repetição |
| Sobre mim | não | texto livre de várias linhas |
| Interesses | não | lista de tags |
| Posso ajudar com | não | lista de tags |
| Procuro ajuda com | não | lista de tags |
| GitHub | não | só perfil, ver 5.3 |
| LinkedIn | não | só perfil, ver 5.3 |

Disciplinas atuais são obrigatórias para **salvar**, como no protótipo (asterisco e erro "Selecione pelo menos uma disciplina atual para completar seu perfil."). Na prática, o Perfil só fica incompleto até o primeiro salvamento.

### 5.2 Tags

- O estudante digita e pressiona Enter para adicionar; o "×" de cada chip remove.
- Espaços nas pontas são removidos; tag vazia é ignorada.
- Tag repetida (sem diferenciar maiúsculas e minúsculas) é ignorada, no front e no servidor.
- Não há sugestões nesta entrega.

### 5.3 GitHub e LinkedIn

Decisão do Marcelo: só o endereço do **perfil da pessoa**. Validação no servidor (`pendencias.md` 2.10), não só no front.

| Campo | Aceita | Salva como | Mostra como |
|---|---|---|---|
| GitHub | `github.com/usuario`, com ou sem `http://`/`https://` e `www.`, com ou sem barra final | `https://github.com/usuario` | `github.com/usuario ↗` |
| LinkedIn | `linkedin.com/in/usuario`, com ou sem `http://`/`https://`, com `www.` ou subdomínio de país de duas letras (`br.`, `pt.`…), com ou sem barra final | `https://www.linkedin.com/in/usuario` | `linkedin.com/in/usuario ↗` |

- Query string (`?utm_source=share…`) e fragmento (`#…`) são **descartados** nos dois campos, e não recusados (decisão do Marcelo, para aceitar o link de compartilhamento do app).
- Recusa outros domínios, repositórios, posts e páginas de empresa.
- O nome de usuário do GitHub segue a regra do GitHub (letras, números e hífen, até 39 caracteres, sem hífen nas pontas). O do LinkedIn aceita letras, números, hífen e sublinhado.
- Campo vazio apaga o link.
- Na visualização, os links abrem em nova guia (`target="_blank"`, `rel="noopener noreferrer"`) e trazem o "↗", com texto acessível de que abrem em nova guia.
- Mensagens de erro: "Informe o endereço do seu perfil no GitHub, como https://github.com/usuario." e "Informe o endereço do seu perfil no LinkedIn, como https://www.linkedin.com/in/usuario."

### 5.4 Teto técnico provisório

Os limites definitivos continuam pendentes (`pendencias.md` 2.7) e serão definidos pelo Marcelo. Até lá, o servidor aplica um teto só de segurança, para recusar abusos (por exemplo, um "Sobre mim" de megabytes):

| Item | Teto provisório |
|---|---|
| Nome de exibição | 120 caracteres (limite já existente) |
| Sobre mim | 5.000 caracteres |
| Cada tag | 255 caracteres |
| Quantidade de tags por campo | 50 |
| Disciplinas atuais | o tamanho do catálogo (não há teto próprio) |

- Não aparece contador na tela.
- No código, cada teto fica num lugar só, com o comentário `shortcut: teto provisório até a definição de pendencias.md 2.7`.
- `pendencias.md` 2.7 registra que esses valores são provisórios.

### 5.5 Quem vê o quê

Nesta entrega, cada estudante vê e edita **só o próprio** Perfil. As notas de visibilidade do protótipo ("As informações públicas do seu perfil podem ser vistas apenas por estudantes autenticados no ConectaCC. Seu e-mail não é exibido." e "Visíveis para outros estudantes autenticados no ConectaCC.") aparecem como no protótipo, porque descrevem a regra que o Perfil público vai seguir.

### 5.6 Tela: visualização

Seguir as telas 1 e 2 do PDF (desktop) e 3 (mobile), sem o Semestre:

- Título "Perfil", texto "Atualize as informações que ajudam a personalizar sua experiência no ConectaCC." e botão "Editar perfil".
- **Aviso "Perfil incompleto"** (quando faltar disciplina): "Selecione pelo menos uma disciplina atual para completar seu perfil." e o botão "Selecionar disciplinas", que abre a edição com o foco na busca de disciplinas.
- **Cabeçalho**: avatar de iniciais, Nome de exibição, "Ciência da Computação" e a nota de visibilidade.
- **Identidade**: Nome de exibição e Curso.
- **Minha jornada acadêmica**: Disciplinas atuais em chips, ou "Nenhuma disciplina atual selecionada.".
- **Sobre mim**: o texto (quebras de linha preservadas) e Interesses em chips.
- **Apoio entre estudantes**: "Ajuda outros estudantes a encontrar apoio na Comunidade.", Posso ajudar com e Procuro ajuda com.
- **Links**: GitHub e LinkedIn.
- Campo vazio mostra "Não informado." (ou "Não informado" nos links, como no protótipo).

### 5.7 Tela: edição

Seguir as telas 4 a 6 e 8 do PDF:

- O botão "Editar perfil" dá lugar ao selo "Editando".
- O cabeçalho mostra o Nome de exibição **enquanto é digitado**; com o nome vazio, mostra "Nome de exibição" e o avatar "?" (tela 7).
- **Nome de exibição**: ajuda "É assim que seu nome aparecerá para outros estudantes no ConectaCC.".
- **Curso**: campo desabilitado com cadeado.
- **Disciplinas atuais**: ajuda "Selecione as disciplinas que você está cursando neste semestre.", nota de visibilidade, campo "Buscar disciplina..." com lista suspensa e, abaixo, "Selecionadas (N)" com chips removíveis, ou "Nenhuma disciplina selecionada.".
  - A lista suspensa mostra as disciplinas do catálogo ainda não selecionadas, em ordem de semestre e depois de nome.
  - A busca filtra pelo nome sem diferenciar acentos nem maiúsculas.
  - Teclado: setas navegam, Enter seleciona, Esc fecha (padrão combobox do WAI-ARIA).
  - Sem a nota "Lista de demonstração do protótipo…", porque agora o catálogo é o oficial.
- **Sobre mim**: ajuda "Conte um pouco sobre seus interesses acadêmicos ou profissionais.".
- **Interesses / Posso ajudar com / Procuro ajuda com**: ajuda "Digite um assunto e pressione Enter para adicionar.", placeholders "Adicionar interesse" e "Adicionar assunto".
- **Links**: placeholders `https://github.com/usuario` e `https://www.linkedin.com/in/usuario`.
- **Barra de ações** fixa no rodapé com "Cancelar" e "Salvar alterações"; no mobile, os botões ficam empilhados e ocupam a largura (tela 4).

### 5.8 Salvar, erros e sucesso

- **Salvando**: o botão vira "Salvando..." com indicador e fica desabilitado; o envio não é duplicado.
- **Sucesso**: volta para a visualização com o aviso verde "Perfil atualizado com sucesso.", que pode ser fechado no "×". O nome e as iniciais do header mudam na hora.
- **Erro de validação**: aviso vermelho "Revise os campos indicados antes de salvar.", erro embaixo de cada campo (borda vermelha), foco no primeiro campo com erro. O que foi digitado é mantido.
- **Falha** (rede ou erro do servidor): aviso vermelho "Não foi possível salvar suas alterações. Tente novamente.", continua em edição, dados mantidos.
- Mensagem de nome vazio: "Informe seu nome de exibição.".

### 5.9 Alterações não salvas (RF-25)

- Ao clicar em "Cancelar" com alterações, ou ao tentar ir para outra página do app (sidebar, header, voltar do navegador), abre o diálogo da tela 11: "Descartar alterações?", "As alterações feitas no seu perfil ainda não foram salvas.", botões "Continuar editando" e "Descartar alterações" (vermelho).
- Sem alterações, "Cancelar" volta direto para a visualização.
- "Sair" (logout) durante a edição com alterações também pede a confirmação **antes** de encerrar a sessão. O redirecionamento automático para `/login` quando a sessão expira (401) não pede confirmação.
- Ao fechar ou recarregar a aba com alterações, vale o aviso padrão do navegador.
- O diálogo devolve o foco ao elemento de origem ao fechar (mesmo cuidado já aplicado ao modal dos Termos).

### 5.10 Carregamento

Enquanto o Perfil carrega, skeletons no lugar dos cards (identidade visual §19). Se a carga falhar, aviso com "Tentar novamente", no mesmo padrão do erro parcial da Home.

## 6. Card "Complete seu perfil" na Home

Seguir a tela 1 do PDF da Home:

- Aparece enquanto o Perfil estiver incompleto (sem Nome de exibição ou sem disciplina atual) e some quando ficar completo (`decisoes.md` §8). Sem porcentagem nem gamificação.
- Conteúdo: ícone, "Complete seu perfil", "Selecione pelo menos uma disciplina atual para personalizar sua experiência no ConectaCC." e o botão "Completar perfil".
- "Completar perfil" abre o Perfil em edição com o foco na busca de disciplinas (`fluxos.md` §4).
- Desktop: fica à direita do card de apresentação, que encolhe, como no protótipo. Mobile: fica logo abaixo do card de apresentação (o protótipo não mostra o mobile com o card).
- Enquanto `/me` carrega, o card não aparece (não pisca).

## 7. Backend

### Dados

- Tabela de **disciplinas**: nome (único), semestre, carga horária, link da ementa.
- Tabela de ligação **disciplinas atuais** (usuário ↔ disciplina), sem repetição; apagar o usuário apaga as ligações.
- Em **users**: Sobre mim (texto, opcional), as três listas de tags (JSON), GitHub e LinkedIn (opcionais). `display_name` já existe.

### API (prefixo `/api/v1`, autenticada)

| Método e rota | Faz |
|---|---|
| `GET /disciplines` | lista o catálogo (id, nome, semestre), ordenado por semestre e nome |
| `GET /profile` | devolve o próprio Perfil |
| `PUT /profile` | valida e grava o Perfil inteiro; devolve o Perfil salvo |
| `GET /me` | passa a incluir `profile_complete` (booleano), usado pela Home |

- Erros de validação no formato 422 já usado no Cadastro, com as mensagens em português desta spec.
- O `PUT` grava tudo numa transação (campos e disciplinas atuais).
- Os nomes exatos de campos no JSON ficam a cargo de quem implementa, seguindo o padrão `snake_case` existente; os tipos do front são regenerados com `make api-types`.
- `PUT /profile` não tem limite de tentativas próprio: é autenticado e só altera a própria conta.

### Seeders de desenvolvimento

As contas de teste continuam como estão (`aluno`, `moderador`, `moderador2`, `admin`), com Perfil **incompleto**, para o card da Home aparecer e o fluxo poder ser testado.

## 8. Frontend

- Rota `/perfil` passa a ser a tela real (sai da lista de "Em construção").
- "Selecionar disciplinas" e "Completar perfil" levam a `/perfil` em modo de edição com foco nas disciplinas; o jeito de sinalizar isso na URL fica a cargo de quem implementa.
- Peças novas reaproveitáveis: campo de tags e busca de disciplinas (vão servir à Comunidade e aos Materiais).
- Reaproveitar o que existe: `TextField`, `FormAlert`, `Skeleton`, `Dialog`, `Button`, `UserAvatar`, `applyFieldErrors`, o cliente gerado do OpenAPI e o TanStack Query (depois de salvar, atualizar `me` e `profile`).
- Acessibilidade: labels associados, `aria-invalid` e `aria-describedby` nos erros, foco visível, estado "Editando" anunciado, chips removíveis com nome acessível ("Remover Git"), alvos de toque adequados no mobile.

## 9. Testes

- **Pest**, contra PostgreSQL, como na fundação:
  - catálogo: a migration grava as 49 disciplinas, sem eletivas, com o nome corrigido; rodar duas vezes não duplica;
  - `GET /disciplines` e `/profile` exigem login;
  - `PUT /profile`: nome obrigatório e com espaços removidos; pelo menos uma disciplina; disciplina inexistente recusada; disciplinas repetidas não duplicam; tags com espaços, vazias e repetidas tratadas; tetos provisórios recusam o excesso; GitHub e LinkedIn aceitam as variações da seção 5.3, normalizam e recusam os casos inválidos;
  - `/me` traz `profile_complete` falso para conta nova e verdadeiro depois de salvar.
- **Frontend**: continua sem Vitest e Playwright no repositório (decisão 11 da stack). A verificação manual dos fluxos é feita com roteiros Playwright headless fora do repositório, como na fundação: ver, editar, salvar, erro de validação, falha, descartar alterações, card da Home sumindo, desktop e mobile.
- `make lint` limpo.

## 10. Documentação a atualizar

- `decisoes.md`: nova decisão "referência, não cópia" (seção 3) e as decisões desta conversa (eletivas fora, Semestre fora, sugestões fora, nome corrigido, links só de perfil).
- `implementacao.md` §6 e §7: marcar o que foi feito.
- `pendencias.md` 1.3 (catálogo carregado, piloto ainda em aberto), 2.7 (tetos provisórios) e 2.10 (resolvida).
- `requisitos.md` RF-31: catálogo com as obrigatórias da Matriz 2023.
- `operacao.md`: como atualizar o catálogo (nova migration).

## 11. Critérios de aceite

1. Um estudante recém-cadastrado vê o card "Complete seu perfil" na Home e o aviso "Perfil incompleto" no Perfil.
2. Pelo card ou pelo aviso, chega à edição com o foco na busca de disciplinas, encontra uma disciplina digitando parte do nome sem acento, salva e vê "Perfil atualizado com sucesso.".
3. Depois disso, o card da Home some e o header mostra o novo nome e as novas iniciais.
4. Salvar sem nome ou sem disciplina mostra os erros da tela 7 e não grava nada.
5. Um link do GitHub ou do LinkedIn que não seja de perfil é recusado pelo servidor, mesmo enviado direto à API.
6. Sair da edição com alterações pede confirmação; "Continuar editando" mantém tudo.
7. As telas batem com o protótipo no desktop e no mobile, sem o Semestre e sem as sugestões.
8. Testes Pest passando e `make lint` limpo.
