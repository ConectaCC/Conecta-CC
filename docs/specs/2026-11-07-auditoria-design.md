# Auditoria — design

Data: 07/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #14)

Referência visual: o protótipo não tem a auditoria. A tela segue o layout atual do `/admin` (`decisoes.md` 65.6). Itens marcados **(escolha do Claude)** podem ser trocados na revisão.

## 1. Intenção

Hoje, Moderações mostra como cada material está agora, e a mudança de papel só fica no log técnico do Render. O ADMINISTRADOR não consegue ver o que aconteceu no sistema, em ordem e de forma confiável.

Esta entrega cria um registro de eventos que não pode ser alterado depois de gravado, e uma tela de consulta no `/admin`. Assim ficam guardados os passos intermediários, a versão anterior dos materiais alterados e as ações sobre contas, e o passado continua visível quando os dados mudarem.

## 2. Escopo

### Entra

Um evento para cada uma destas ações, gravado na mesma transação da ação:

| Ação | Quem fez | Sobre |
|---|---|---|
| Cadastro | a própria pessoa | a conta |
| Troca de senha | a própria pessoa | a conta |
| Alteração de papel pelo painel | o ADMINISTRADOR | a conta |
| Alteração de papel pelo workflow | "Workflow do GitHub" | a conta |
| Envio de material | o autor | o material |
| Envio de alteração | o autor | a alteração |
| Cancelamento de envio | o autor | o material ou a alteração |
| Exclusão de material | o autor | o material |
| Aprovação | quem decidiu | o material ou a alteração |
| Rejeição | quem decidiu | o material ou a alteração |

Também entra a tela **Auditoria** no `/admin`, só de leitura.

### Não entra

- **Login, logout e consultas:** são muitos eventos, quase todos dado pessoal, e pouco úteis no piloto. Isso inclui o ADMINISTRADOR ver o autor de um material anônimo.
- **Histórico anterior a esta entrega:** a auditoria começa a contar a partir do deploy. As decisões antigas continuam em Moderações.
- **Limpeza automática:** a regra final de retenção entra na revisão dos Termos e da LGPD (`pendencias.md` 3.1).
- **Exportação e alertas:** futuro.

## 3. Decisões (07/11/2026)

1. **O que entra:** as ações da seção 2, ou seja, tudo o que muda dados. Login, logout e consultas ficam de fora.
2. **O que cada evento guarda:** quem fez, a ação, a conta ou o material afetado, a data e um resumo curto da mudança. Sem IP, e-mail, senha, hash, token, sessão ou o texto do motivo da rejeição, que já fica no material.
3. **Versão anterior:** a aprovação de uma alteração guarda o título, a descrição e o link que estavam publicados antes.
4. **Quem vê:** só o ADMINISTRADOR, na tela Auditoria do `/admin`, com filtros por ação, por pessoa e por período. Ninguém edita nem apaga um evento, nem pelo painel. O MODERADOR não vê.
5. **Workflow:** a alteração de papel pelo workflow aparece como "Workflow do GitHub".
6. **Retenção:** os eventos ficam guardados durante o piloto, sem limpeza automática. Quando a exclusão de conta for implementada (§64), o evento continua existindo, e a pessoa aparece como "Conta excluída".
7. **Histórico antigo:** não é reconstruído.
8. **Logs técnicos:** a linha `Papel alterado` continua no log, para operação. A auditoria passa a ser o registro oficial.
9. **Envio anônimo:** a auditoria mostra quem enviou, com a marca "Enviado anonimamente", como em Moderações (`decisoes.md` 55.2).

## 4. Backend

### Tabela `audit_events`

| Coluna | Conteúdo |
|---|---|
| `id` | — |
| `action` | código da ação, com `CHECK` no banco para os valores da seção 2 |
| `actor_id` | quem fez. Nulo só quando foi o workflow |
| `user_id` | a conta afetada, quando houver |
| `material_id` | o material ou a alteração afetada, quando houver |
| `data` | JSON com o resumo da seção 4 |
| `created_at` | data e hora do evento |

- **Sem `updated_at`:** o evento não muda.
- **Chaves estrangeiras** para `users` e `materials`, com `restrict` na exclusão, como em `materials`. Pela §64, a exclusão de conta anonimiza a conta em vez de apagá-la.
- **Proteção no banco:** uma trigger do PostgreSQL recusa `UPDATE` e `DELETE` em `audit_events` **(escolha do Claude)**. Assim, nem um bug, nem o painel, nem um `tinker` apagam o histórico sem desligar a trigger de propósito.
- **Índices:** por `created_at`, `action` e `actor_id`, que são os filtros da tela.

### O que vai em `data`

| Ação | `data` |
|---|---|
| Alteração de papel | `from` e `to`, e `via`: `painel` ou `workflow` |
| Envio, alteração, cancelamento, exclusão, aprovação e rejeição | `anonymous` (se o envio era anônimo) e `revision` (se é uma alteração) |
| Aprovação de alteração | também `previous`: `title`, `description` e `url` publicados antes |
| Cadastro e troca de senha | vazio |

O código e o título do material vêm da própria tabela `materials`, que não apaga registros.

### Gravação

- Uma classe `RecordAuditEvent` (ou equivalente) é chamada pelas Actions: `RegisterUser`, `ChangePassword`, `ChangeUserRole`, `CreateMaterial`, `ReviseMaterial`, `CancelMaterial`, `DeleteMaterial`, `ApproveMaterial` e `RejectMaterial`, além do comando `PromoteUser`.
- O evento é gravado na mesma transação da ação. Se a ação não vale (por exemplo, uma decisão que chegou tarde), nenhum evento é gravado.
- O `PromoteUser` passa a usar a mesma regra de gravação, com quem fez nulo e `via: workflow`. Papel igual ao atual não gera evento.

## 5. Tela "Auditoria" (`/admin/auditoria`)

Terceiro item do menu, depois de Usuários e Moderações. O subtítulo é "Consulte o que foi feito no sistema e por quem.".

| Coluna | Conteúdo |
|---|---|
| Data | data e hora |
| Ação | o nome em português (seção 6) |
| Quem fez | nome de exibição, "Workflow do GitHub" ou "Conta excluída". Em envio anônimo, mostra a marca "Enviado anonimamente" abaixo |
| Sobre | a conta (nome de exibição) ou o material (`MAT-00012 · título`) |
| Resumo | por exemplo, "Aluno → Moderador" ou "Alteração" |

- **Ordem:** mais recentes primeiro.
- **Filtros:** ação, quem fez e período (de/até).
- **Detalhe ("Ver"):** mostra tudo da linha. Numa aprovação de alteração, mostra também a versão anterior.
- **Sem ações que alterem dados.** Mesmas regras de largura do painel: sem rolagem lateral e empilhada no celular.

## 6. Textos das ações

| Código | Texto |
|---|---|
| `user_registered` | Cadastro |
| `password_changed` | Senha alterada |
| `role_changed` | Papel alterado |
| `material_submitted` | Material enviado |
| `revision_submitted` | Alteração enviada |
| `submission_cancelled` | Envio cancelado |
| `material_deleted` | Material excluído |
| `material_approved` | Aprovado |
| `material_rejected` | Rejeitado |

## 7. Testes

### Backend (Pest)

- Cada ação da seção 2 grava exatamente um evento, com quem fez, sobre o quê e `data` corretos.
- Uma ação que falha ou chega tarde não grava evento: decisão sobre envio cancelado, cancelamento de envio já decidido e validação recusada.
- A aprovação de alteração guarda a versão anterior, e não a nova.
- `PromoteUser` grava com quem fez nulo e `via: workflow`. Papel igual não grava nada.
- Nenhum evento guarda e-mail, senha, hash ou token. O teste confere o JSON de cada tipo.
- O banco recusa `UPDATE` e `DELETE` em `audit_events`.
- Tela: só ADMINISTRADOR. Lista, filtros por ação, pessoa e período, autor real com a marca no envio anônimo, detalhe com a versão anterior e nenhuma ação que altere dados.

### Navegador (Playwright fora do repositório)

- Fluxo real: enviar um material anônimo, aprovar, alterar e aprovar a alteração, e trocar um papel. Conferir todos os eventos na tela.
- Sem rolagem lateral de 320 a 1920 px.

## 8. Documentação a atualizar

- `decisoes.md`: nova seção com as decisões da seção 3.
- `requisitos.md` §1.3: nota de que a auditoria do MVP é esta.
- `pendencias.md` §6 e `roadmap.md`: a auditoria saiu do futuro. Continuam futuras a auditoria de consultas e de login, a exportação e a retenção final.
- `implementacao.md` §24, `status-mvp.md`, `operacao.md` e `README.md`.
- Regra para entregas futuras: toda ação administrativa sensível nova (reversão, retirada, exclusão de conta) entra junto com o evento de auditoria dela.

## 9. Fica para o builder

- O nome exato da classe de gravação e como as Actions a recebem.
- Como mostrar "Conta excluída" quando a exclusão existir. Hoje, toda conta existe.
- O formato exato da trigger e da migration.
