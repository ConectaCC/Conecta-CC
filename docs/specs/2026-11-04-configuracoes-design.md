# Configurações — design

Data: 04/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #10)

Referência visual: protótipo, artboards `Configuracoes` e `Cfg-*`. Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Hoje a sidebar leva a uma página "Em construção", e ninguém consegue trocar a senha pelo app. Esta entrega substitui o placeholder pela página de Configurações, com só o que o MVP precisa (`decisoes.md` 28): ver o e-mail institucional e alterar a senha.

## 2. Escopo

### Entra

- Página `/configuracoes`, para todos os papéis.
- **Conta:** e-mail institucional somente leitura.
- **Segurança:** alterar senha com Senha atual, Nova senha e Confirmar nova senha.
- Encerramento das outras sessões depois da troca.
- Limite de tentativas na troca de senha.

### Não entra

- **"Esqueci minha senha":** continua opcional (`decisoes.md` 52.9) e será reavaliado depois do painel `/admin`. Até lá, quem esquecer a senha não consegue recuperá-la pelo app, inclusive durante o piloto.
- **E-mail avisando da troca de senha:** fica para decisão futura (`pendencias.md` 1.7).
- **Tema, idioma, preferências e exclusão de conta:** fora do MVP (`mvp.md` 12).

## 3. Decisões (04/11/2026)

1. **Outras sessões:** a troca encerra as outras sessões da conta, e a pessoa continua logada no aparelho atual. Se a recuperação de senha entrar depois, ela também encerra as outras sessões. Isso resolve `pendencias.md` 2.5.
2. **Nova senha igual à atual:** é aceita. As regras continuam sendo só 8 caracteres e confirmação igual (`decisoes.md` 30).
3. **Limite:** 5 tentativas por minuto, com a mesma mensagem do login.
4. **Aviso por e-mail:** não é enviado no MVP. A decisão fica registrada como pendência futura.
5. **Recuperação de senha:** fica fora desta entrega.

## 4. Backend

### Dado do usuário

- **`UserResource`** ganha `email`. Nenhuma resposta da API trazia o e-mail, e a página precisa exibi-lo. Esse recurso só é devolvido para o próprio usuário. `StudentProfileResource` continua sem e-mail.

### Rota

**`PUT /api/v1/me/password`**, dentro de `auth:sanctum`, com `throttle:password`. Corpo: `current_password`, `password` e `password_confirmation`. Esses nomes já ficam fora do corte de espaços do Laravel, então espaços na senha são preservados.

| Situação | Resposta |
|---|---|
| Sucesso | 204, sem corpo |
| Campo inválido ou senha atual errada | 422 com os erros por campo |
| Mais de 5 tentativas no minuto | 429 com "Muitas tentativas. Tente novamente em N segundos." |
| Sem sessão | 401 |

### Mensagens de validação (`ChangePasswordRequest`)

| Campo | Situação | Mensagem |
|---|---|---|
| `current_password` | vazio | Informe sua senha atual. |
| `current_password` | incorreta | A senha atual informada está incorreta. |
| `password` | vazio | Informe sua nova senha. |
| `password` | menos de 8 caracteres | Use pelo menos 8 caracteres. |
| `password_confirmation` | vazio | Confirme sua nova senha. |
| `password_confirmation` | diferente | As senhas informadas não coincidem. |

### Regra (`ChangePassword`)

- Na mesma transação, grava a nova senha e apaga da tabela `sessions` as linhas do usuário cujo id é diferente da sessão atual.
- Depois da transação, o controller regenera o id da sessão atual e destrói o antigo (`regenerate(true)`).

> **Correção (04/11/2026):** a spec dizia para regenerar "como o login já faz". Assim, a linha antiga continuaria em `sessions` até expirar. O `AuthenticateSession` já recusaria esse cookie pela divergência do hash da senha; apagar a linha é uma camada extra.
- Produção e desenvolvimento usam `SESSION_DRIVER=database`, então apagar as linhas encerra as sessões.

### Limite (`throttle:password`)

Registrado em `AppServiceProvider`, ao lado de `login` e `register`: 5 por minuto, por id do usuário **(escolha do Claude)**. Conta todas as tentativas e reaproveita `tooManyAttempts`.

Mudou a API: rodar `make api-types`.

## 5. Frontend

Pasta `features/settings/`, com `SettingsPage.tsx` e `hooks.ts`. A rota `/configuracoes` sai dos `placeholders` de `router.tsx`. Como esse era o último item, a lista e `UnderConstructionPage` deixam de ser usadas e são removidas.

### Textos

- **Cabeçalho:** "Configurações" e "Gerencie informações da sua conta e opções de segurança.".
- **Conta:** "Conta", "E-mail institucional" e "Dado da conta. Não pode ser alterado nesta versão.".
- **Segurança:** "Segurança", "Alterar senha" e "Informe sua senha atual e escolha uma nova senha.".
- **Campos:** "Senha atual", "Nova senha" (ajuda: "Use pelo menos 8 caracteres.") e "Confirmar nova senha", os três com o asterisco de obrigatório usado no Perfil.
- **Botão:** "Salvar nova senha", e "Salvando…" com spinner durante o envio.
- **Sucesso:** "Senha alterada com sucesso.".
- **Falha de rede ou de servidor:** "Não foi possível alterar sua senha agora. Tente novamente.".
- **Saída com dados preenchidos:** "Descartar alterações?", "As alterações feitas ainda não foram salvas.", "Continuar editando" e "Descartar alterações".

### Comportamento

- **E-mail:** vem de `useMe()`. O guard de autenticação não espera essa consulta, então a página mostra um esqueleto enquanto ela carrega e "E-mail indisponível no momento." se falhar **(escolha do Claude)**. O `AppLayout` já mostra o aviso com "Tentar novamente".

> **Correção (04/11/2026):** a spec dizia que o e-mail já vinha carregado pelo guard, e a implementação mostrou que não vem.
- **Validação no navegador:** as mesmas regras e mensagens da seção 4, antes de enviar. O backend continua sendo a fonte de verdade.
- **Sucesso:** mostra o aviso verde, limpa os três campos e volta a ocultar as senhas.
- **Senha atual incorreta:** mostra o erro no campo, limpa só esse campo e leva o foco até ele.
- **422 em outros campos:** `applyFieldErrors`.
- **429:** `FormAlert` vermelho com a mensagem da API. Os campos são mantidos.
- **Falha de rede ou de servidor:** `FormAlert` vermelho. Os campos são mantidos.
- **Saída com campo preenchido:** usa a mesma guarda do Perfil (trocar de página, voltar do navegador, Sair e fechar a aba).
- **Gerenciador de senhas:** o formulário leva um campo oculto de e-mail com `autocomplete="username"`, e os campos usam `current-password` e `new-password`.

### Componente compartilhado

`TextField` ganha uma prop opcional com o nome do campo no botão de mostrar/ocultar, porque três botões iguais confundiriam o leitor de tela. Sem a prop, continua "Mostrar senha". Aqui os rótulos são "Mostrar senha atual", "Mostrar nova senha" e "Mostrar confirmação da nova senha", com os equivalentes "Ocultar…".

### Diferenças em relação ao protótipo

- O aviso de sucesso usa o botão de fechar do `FormAlert` ("Fechar aviso"), não "Fechar mensagem" **(escolha do Claude)**.
- O aviso de falha usa o `FormAlert` vermelho de erro **(escolha do Claude)**.

## 6. Testes

### Backend (Pest)

- Troca com sucesso: 204, a nova senha funciona no login e a antiga não.
- Nova senha igual à atual é aceita.
- Cada mensagem de validação da seção 4, e a senha continua a mesma quando há erro.
- Encerra as outras sessões da conta, mantém as de outros usuários e não apaga nada quando a troca falha.
- Sexta tentativa no minuto responde 429 com a mensagem certa, e o limite é por usuário.
- Sem sessão responde 401.
- `GET /me` traz o e-mail, e o Perfil público continua sem ele.

### Frontend (Playwright fora do repositório, em 1440×900, 390×844 e 320 px)

- Dois navegadores na mesma conta: o primeiro troca a senha e continua logado; o segundo perde a sessão e vai para o Login.
- A senha nova entra e a antiga não.
- Validação de campos, senha atual errada (limpa e foca), 429 e falha de rede com os campos mantidos.
- Campos limpos e ocultos depois do sucesso.
- "Descartar alterações?" ao sair com campo preenchido.
- E-mail exibido e somente leitura. Sem rolagem horizontal.
- O botão de mostrar/ocultar do Login e do Cadastro continua igual.

## 7. Documentação a atualizar

- `decisoes.md`: nova seção com as decisões da seção 3.
- `pendencias.md`: atualização em 2.5 (resolvida). O item 1.7, do aviso por e-mail, já entra junto com esta spec.
- `implementacao.md` §13 e a ordem de implementação, `status-mvp.md`, `roadmap.md`, `README.md` e `fluxos.md` §22 (outras sessões encerradas).
- `requisitos.md` RF-59: nota sobre o encerramento das outras sessões.
- `backend/AGENTS.md` e `frontend/AGENTS.md`, se citarem o placeholder ou `UnderConstructionPage`.

## 8. Fica para o builder

- Como reaproveitar a guarda de saída do `ProfileForm` sem duplicá-la. Ela hoje tem uma regra própria do modo de edição do Perfil, que precisa continuar funcionando.
- O nome exato da prop do `TextField`.
