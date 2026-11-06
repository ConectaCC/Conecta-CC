# Painel `/admin` — design

Data: 05/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #13)

Referência visual: o protótipo não tem o painel. O visual segue o layout do app: sidebar azul-marinho de altura total, cabeçalho branco, fundo claro, fontes e tags de status iguais.

> **Correção (05/11/2026):** a spec previa o visual padrão do Filament com as cores e o logo do ConectaCC. Na revisão, o time pediu um layout mais parecido com o resto do sistema.

 Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Hoje, mudar o papel de alguém exige disparar um workflow no GitHub, porque o Render gratuito não tem terminal. Quem administra também não tem onde consultar o histórico de moderações. Esta entrega cria o painel exclusivo do ADMINISTRADOR, com só essas duas funções.

## 2. Escopo

### Entra

- Painel Filament 5 em `/admin`, só para ADMINISTRADOR, em português.
- **Usuários:** lista, busca e alteração de papel.
- **Moderações:** consulta somente leitura dos materiais.
- Link "Painel administrativo" na sidebar do app, só para ADMINISTRADOR.

### Não entra

- **Exclusão de conta:** a política está em `decisoes.md` 64, e a implementação fica fora do MVP.
- **Retirada, revisão e reversão de decisões de Moderação:** pós-MVP (`decisoes.md` 63).
- **Catálogo de disciplinas no painel:** continua vindo das migrations.
- **Tela inicial com contadores:** o painel abre em Usuários.
- **Edição de nome, e-mail ou perfil pelo painel:** esses dados só aparecem para consulta.
- **Redefinição de senha pelo ADMINISTRADOR:** ele ficaria sabendo uma senha de outra pessoa. "Esqueci minha senha" será reavaliado depois do painel (`decisoes.md` 52.9).
- **Tela de auditoria e histórico:** futuro (`roadmap.md`, Administração).
- **Permissões individuais:** "gerenciar permissões" no MVP significa definir o papel da pessoa.

## 3. Decisões (05/11/2026)

1. **Exclusão de conta:** a política entra nos documentos, e a implementação não entra no MVP, nem como ação no painel (`decisoes.md` 64).
2. **Papéis:** o ADMINISTRADOR pode definir ALUNO, MODERADOR ou ADMINISTRADOR em outras contas, com confirmação, e não pode mudar o próprio papel. O workflow "Promover usuário" continua como saída de emergência.
3. **Acesso:** o painel tem login próprio em `/admin/login` e usa a mesma sessão do app. Quem já entrou no app como ADMINISTRADOR não precisa logar de novo. Os demais papéis recebem "sem permissão" (403) ao abrir `/admin`.
4. **Fora desta entrega:** catálogo de disciplinas, tela inicial com contadores, edição de nome e e-mail e redefinição de senha.

Decisões anteriores que valem aqui:

- O ADMINISTRADOR vê o nome do autor de material anônimo, com a marca "Enviado anonimamente" (`decisoes.md` 55.2). O MODERADOR continua sem acesso a essa informação, porque não entra no painel.
- As cores de status seguem a mesma semântica do app (`decisoes.md` 58).

## 4. Backend

### Acesso

- `User` implementa `FilamentUser`. `canAccessPanel()` só libera `Role::Administrador`.
- A verificação roda a cada requisição, então quem perde o papel perde o acesso na requisição seguinte.
- O login só aceita ADMINISTRADOR, e o e-mail é normalizado como no login do app (sem espaços nas pontas e em minúsculas). Tem o título "Painel administrativo" e o botão "Entrar", sem "Lembrar de mim" **(escolha do Claude)**.
- O painel não tem cadastro, perfil, recuperação de senha, busca global, notificações em banco nem modo escuro **(escolha do Claude)**.

### Usuários (`/admin/usuarios`)

| Coluna | Origem |
|---|---|
| Nome | `name`, com `display_name` abaixo |
| E-mail | `email` |
| Papel | `role`, em português |
| Cadastro | `created_at`, só a data e em telas a partir de 1280 px |

- **Ordem:** cadastros mais recentes primeiro **(escolha do Claude)**.
- **Busca:** nome, nome de exibição e e-mail.
- **Filtro:** por papel **(escolha do Claude)**.
- **Sem criação, edição geral ou exclusão.**

**Ação "Alterar papel":**

- Abre um modal com o papel atual selecionado.
- Texto do modal: "Quem for definido como ADMINISTRADOR passa a acessar este painel e a identidade dos autores de materiais anônimos."
- Botões: "Salvar papel" e "Cancelar".
- Sucesso: aviso "Papel atualizado.".
- Na própria linha, a ação não aparece.

**Regra (`ChangeUserRole`):**

- A Action recebe quem altera, a conta e o novo papel. Se quem altera for a própria conta ou não for ADMINISTRADOR, responde 403, mesmo que a tela tenha sido burlada.
- Papel igual ao atual não grava nem registra nada.
- Cada mudança gera uma linha de log com `actor_id`, `target_id`, papel anterior e novo papel, sem e-mail ou nome **(escolha do Claude)**.

### Moderações (`/admin/moderacoes`)

Inclui todos os materiais, inclusive excluídos e alterações. Não há ação que altere dados.

| Coluna | Conteúdo |
|---|---|
| Material | título, com `MAT-00001 · Disciplina` abaixo |
| Autor | nome de exibição, com "Enviado anonimamente" quando for o caso |
| Situação | Pendente, Aprovado, Rejeitado, Cancelado ou Excluído, mais a marca "Alteração" quando `revises_id` existe |
| Enviado em | `created_at`, só a data e em telas a partir de 1280 px |
| Decisão | nome de exibição de quem decidiu, com a data abaixo, ou "—" |

> **Correção (05/11/2026):** eram oito colunas, que causavam rolagem lateral. As colunas foram agrupadas para caber na tela, e o detalhe continua mostrando todos os campos.

> **Correção (05/11/2026):** a marca "Alteração" estava na coluna Título. Ela foi para Situação, junto com o status, porque a §58 diz que Alteração não substitui o status.

- **Excluído:** `deleted_at` preenchido vale mais que o status, como em `MyMaterialResource`.
- **Ordem:** envios mais recentes primeiro **(escolha do Claude)**.
- **Busca:** título e autor.
- **Filtro:** por situação.
- **Detalhe ("Ver"):** mostra todos os campos da tabela, com data e hora, e também descrição, link (abre em nova aba), motivo da rejeição e, numa alteração, o título e o link originais.
- **Largura:** as duas tabelas não têm rolagem lateral de 320 a 1920 px, e ficam empilhadas no celular.
- **Cores:** Pendente amarelo, Aprovado verde, Rejeitado e Excluído vermelho, Cancelado cinza e Alteração laranja.

Não muda a API, então `make api-types` não é necessário.

## 5. Frontend (SPA)

- `roles.ts` ganha `isAdmin(role)`, no mesmo formato de `canModerate`.
- A sidebar mostra "Painel administrativo" abaixo de Moderação, só para ADMINISTRADOR.
- É um link comum, com recarga completa da página e na mesma aba **(escolha do Claude)**. Se houver alteração não salva, vale o aviso que o navegador já mostra ao sair.
- O link precisa funcionar em produção e no ambiente de desenvolvimento, em que o app roda em outra porta.

No painel, o rodapé da sidebar tem "Voltar ao ConectaCC", que leva a `/home` **(escolha do Claude)**. Sair do painel encerra a mesma sessão do app.

## 6. Textos e idioma

- **Menu:** "Usuários" e "Moderações". Marca: "ConectaCC".
- **Interface:** usa a tradução `pt_BR` do Filament.
- **Mensagens de validação:** o projeto não tem `lang/pt_BR`, então as validações padrão do Laravel podem aparecer como chave. Nenhuma mensagem pode aparecer em inglês ou como chave.
- **Campo obrigatório no login:** o aviso nativo do navegador segue o idioma do navegador.
- **Página 403:** "Você não tem permissão para acessar esta página." e o link "Voltar ao ConectaCC" **(escolha do Claude)**.

## 7. Produção

- Os arquivos públicos do Filament são gerados no build da imagem e não entram no Git. Hoje o `Dockerfile` usa `--no-scripts` na instalação, então essa etapa precisa ser garantida e testada.
- A rota de fallback da SPA não pode capturar as rotas e os arquivos do Livewire e do Filament.
- O Sentry continua sem enviar e-mail, nome ou corpo de requisição, inclusive nas requisições do Livewire.
- Depois do deploy, conferir no Render se o painel abre e se o serviço não reinicia por falta de memória.

### Limite conhecido

Se dois ADMINISTRADORES retirarem o papel um do outro ao mesmo tempo, o sistema pode ficar sem ADMINISTRADOR. Não vale tratar esse caso raro agora: o workflow "Promover usuário" resolve **(escolha do Claude)**.

## 8. Testes

### Backend (Pest, com Livewire)

- Sem sessão: `/admin` leva a `/admin/login`.
- ALUNO e MODERADOR recebem 403. ADMINISTRADOR recebe 200.
- Sessão criada pelo login do app abre o painel.
- Quem perde o papel de ADMINISTRADOR perde o acesso na requisição seguinte.
- Login do painel aceita ADMINISTRADOR, inclusive com e-mail em maiúsculas, e recusa os demais.
- Depois de trocar a senha em Configurações, a sessão continua valendo no painel, e as outras sessões não.
- Usuários: lista e busca funcionam, e não existe ação de criar, editar ou excluir.
- Alterar papel funciona para os três papéis, sem mudar outros campos.
- A própria linha não tem a ação, e `ChangeUserRole` recusa a mudança do próprio papel.
- A linha de log traz os IDs e não traz e-mail nem nome.
- Moderações: lista materiais em todas as situações, inclusive excluídos e alterações.
- Moderações: mostra o autor e "Enviado anonimamente" em material anônimo.
- Moderações: não tem nenhuma ação que altere dados.
- A rota de fallback continua servindo a SPA e não captura as rotas do painel.

### Frontend e painel (Playwright fora do repositório)

- O link aparece só para ADMINISTRADOR e abre o painel sem novo login.
- Ida e volta entre app e painel, e Sair no painel encerra também o app.
- Fluxo completo de alteração de papel: a pessoa promovida ganha o acesso, e a rebaixada perde.
- Textos em português, inclusive validações e página 403.
- Painel utilizável em 1440×900 e 390×844, sem rolagem horizontal da página.
- Imagem de produção construída localmente: o painel abre com CSS e JavaScript carregados.

## 9. Documentação a atualizar

Já atualizados com a política de exclusão: `decisoes.md` 64, `requisitos.md` RF-61, `pendencias.md` 3.1 e 3.2, `implementacao.md` §13, `stack-e-decisoes-tecnicas.md` decisão 5, `status-mvp.md` e `roadmap.md`.

No fim da entrega:

- `decisoes.md`: nova seção com as decisões da seção 3 e as escolhas desta spec.
- `implementacao.md`: nova seção do painel, com o que cada função pode fazer, porque a decisão 5 da stack aponta para ela. Atualizar também a ordem na §22 e a linha sobre mudança de papel.
- `requisitos.md` §1.3: nota sobre o que o painel do MVP cobre.
- `status-mvp.md`, `roadmap.md` e `README.md`: painel entregue.
- `operacao.md`: acesso ao painel, alteração de papel por ele e papel do workflow como emergência.
- `backend/AGENTS.md` e `frontend/AGENTS.md`: onde fica o código do painel e o link da sidebar.

## 10. Fica para o builder

- A organização das classes do Filament em `app/Filament`.
- Como dispensar as permissões da `MaterialPolicy` nesse painel. O Filament consulta a policy existente, que não tem `viewAny` nem `view`, e o acesso já é limitado ao ADMINISTRADOR.
- Como o link funciona em desenvolvimento (proxy do Vite ou endereço direto).
- Como garantir os arquivos do Filament na imagem de produção.
- Como resolver as validações em português.
- O tom exato das cores do Filament, desde que a semântica da seção 4 seja respeitada e o contraste, legível.
