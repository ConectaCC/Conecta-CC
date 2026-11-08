# Fluxos do ConectaCC

Este documento descreve os principais fluxos funcionais previstos para o MVP do ConectaCC.

Os fluxos representam o comportamento esperado do produto e servem como referência para:

- prototipação;
- implementação;
- testes;
- validação com usuários.


> **Escopo atual (02/11/2026):** Oportunidade, Comunidade (feed, publicações e canais) e a busca de estudantes saíram do MVP e ficam para a próxima versão (`decisoes.md` 56). Os requisitos e fluxos dessas áreas continuam neste documento como referência.

---

# 1. Cadastro

Fluxo principal:

Cadastro  
→ preencher Nome completo  
→ preencher E-mail institucional do IFSul
→ criar Senha  
→ Confirmar senha  
→ validar formulário  
→ abrir Termos de participação  
→ marcar aceite  
→ Concordar e criar conta  
→ criar conta  
→ Login.

Após a criação da conta:

→ o Login pode apresentar a mensagem:

**Conta criada. Entre com seu e-mail e senha.**

O e-mail utilizado pode permanecer preenchido para facilitar o acesso.

No MVP e no piloto:

- o cadastro utiliza exclusivamente e-mail institucional do IFSul;
- não existe confirmação de cadastro por link ou código enviado ao e-mail.

Assim, depois da criação bem-sucedida da conta:

→ o usuário retorna diretamente ao Login.

A confirmação de propriedade do e-mail poderá ser avaliada apenas em uma evolução futura do ConectaCC.
---

# 2. Login

Fluxo:

Login  
→ informar E-mail institucional  
→ informar Senha  
→ Entrar  
→ validar credenciais  
→ Home.

Em caso de credenciais inválidas:

→ permanecer no Login  
→ apresentar mensagem genérica:

**E-mail ou senha incorretos.**

A interface não deve indicar separadamente se o problema está no e-mail ou na senha.

---

# 3. Recuperação de senha

Fluxo:

Login  
→ Esqueci minha senha  
→ informar E-mail institucional  
→ Enviar instruções  
→ Verifique seu e-mail.

A resposta deve ser genérica e não confirmar se existe uma conta associada ao endereço informado.

Exemplo:

**Se existir uma conta associada ao e-mail informado, enviaremos as instruções para redefinir sua senha.**

Depois:

E-mail recebido  
→ acessar link de recuperação  
→ Definir nova senha  
→ Confirmar nova senha  
→ Redefinir senha  
→ Senha redefinida  
→ Ir para o Login.

A tela de definição da nova senha não faz parte da navegação normal da aplicação.

Ela deve ser acessada através do link de recuperação.

A recuperação de senha é independente da confirmação de cadastro.

Mesmo sem confirmação de propriedade do e-mail no MVP/piloto, a recuperação continuará utilizando envio de instruções para o endereço associado à conta.

---

# 4. Primeiro acesso

Após o primeiro Login:

Login  
→ Home.

Como o Nome de exibição já é iniciado com base no Nome completo, a principal informação ainda ausente tende a ser:

**Disciplinas atuais**

A Home apresenta:

**Complete seu perfil**

Fluxo:

Home  
→ Completar perfil  
→ Perfil em modo de edição  
→ foco em Disciplinas atuais  
→ selecionar pelo menos uma disciplina  
→ Salvar alterações  
→ Perfil completo.

Depois:

Perfil completo  
→ Home  
→ card “Complete seu perfil” deixa de aparecer.

---

# 5. Perfil

Acesso normal:

Sidebar  
→ Perfil.

ou:

Avatar/Nome no header  
→ Perfil.

Nesse caso, o Perfil abre inicialmente em modo de visualização.

Fluxo de edição:

Perfil  
→ Editar perfil  
→ alterar informações  
→ Salvar alterações  
→ validar  
→ loading  
→ sucesso  
→ voltar ao modo de visualização.

O Perfil é considerado completo quando possui:

- Nome de exibição;
- pelo menos uma disciplina atual.

---

# 6. Alterações não salvas

Fluxo padrão reutilizado em formulários:

Usuário altera informações  
→ tenta sair da tela sem salvar  
→ abrir confirmação:

**Descartar alterações?**

Ações:

- Continuar editando;
- Descartar alterações.

Se escolher:

**Continuar editando**

→ permanecer no formulário.

Se escolher:

**Descartar alterações**

→ perder alterações locais  
→ continuar para o destino escolhido.

---

# 7. Perfil → Estudos

Fluxo:

Perfil  
→ selecionar Disciplinas atuais  
→ Salvar alterações.

Depois:

Estudos  
→ Minhas disciplinas.

As disciplinas selecionadas no Perfil alimentam a seção:

**Minhas disciplinas**

Se uma disciplina for removida do Perfil:

→ deixa de aparecer em Minhas disciplinas.

Ela pode continuar existindo em:

**Todas as disciplinas**

---

# 8. Estudos

Fluxo principal:

Sidebar  
→ Estudos.

A página apresenta:

- Minhas disciplinas;
- Todas as disciplinas;
- busca de disciplinas.

Ao acessar uma disciplina:

Estudos  
→ selecionar disciplina  
→ Página da disciplina.

A página da disciplina pode apresentar:

- informações da disciplina;
- acesso à ementa oficial, quando houver URL confirmada;
- materiais aprovados;
- opção de compartilhar material (todas as disciplinas — `decisoes.md` 54.2).

A referência acadêmica inicial do MVP será a:

**Matriz 2023 do Bacharelado em Ciência da Computação do IFSul – Câmpus Passo Fundo.**

Ela organiza o curso do:

**1º ao 8º semestre.**

A necessidade de contemplar estudantes vinculados à Matriz 2017 será verificada posteriormente e não altera este fluxo neste momento.

---

# 9. Compartilhar material

Fluxo:

Estudos  
→ Página da disciplina  
→ Compartilhar material.

O formulário já conhece a disciplina de origem.

Campos:

- Título;
- Descrição opcional;
- Link.

Fluxo:

Preencher formulário  
→ Enviar para análise  
→ loading  
→ Material enviado para análise  
→ status PENDENTE.

O material não aparece imediatamente para outros estudantes.

> **Atualização (04/11/2026):** o formulário tem "Compartilhar anonimamente" (`decisoes.md` 55.2). O sucesso avisa que o autor receberá uma notificação e aponta para Meus conteúdos.

## Meus conteúdos, editar, excluir e cancelar

Perfil  
→ Meus conteúdos  
→ filtros Todos, Em andamento, Aprovados, Rejeitados e Excluídos  
→ "Mostrar mais" de 20 em 20.

Em análise  
→ Cancelar envio  
→ confirmar  
→ CANCELADO  
→ sai da fila.

Aprovado  
→ Editar (também pelo menu ⋯ no card, só para o autor)  
→ Enviar para análise  
→ alteração PENDENTE  
→ a versão publicada continua no ar até a aprovação.

Aprovado  
→ Excluir (também pelo menu ⋯)  
→ confirmar  
→ some da disciplina  
→ aparece como Excluído  
→ a alteração pendente é cancelada.

Rejeitado  
→ o autor vê o motivo  
→ para corrigir, faz um envio novo.

> **Atualização (04/11/2026):** as tags seguem uma cor por status: Em análise em amarelo, Aprovado em verde, Rejeitado e Excluído em vermelho, Cancelado em cinza. Item com alteração em análise ou não aprovada mantém o status e ganha também a tag Alteração, em laranja (`decisoes.md` 58).

---

# 10. Moderação de material

Fluxo:

Material enviado  
→ PENDENTE  
→ ADMIN acessa Moderação  
→ abre conteúdo  
→ analisa.

## Se aprovado

ADMIN  
→ Aprovar  
→ confirmar aprovação  
→ APROVADO  
→ material sai da fila  
→ material aparece em Estudos  
→ data pública = data de aprovação/publicação  
→ autor recebe notificação.

Para os demais estudantes, o material continua aparecendo no MVP atual como:

**Compartilhado pela comunidade**

> **Atualização (04/11/2026):** substituído. O material identificado mostra o nome do autor, com link para o Perfil público. Só o material anônimo aparece como "Compartilhado pela comunidade" (`decisoes.md` 55.2 e 57.2).

## Se rejeitado

ADMIN  
→ Rejeitar  
→ motivo obrigatório (`decisoes.md` 55.4)  
→ confirmar rejeição  
→ REJEITADO  
→ material sai da fila  
→ material não é publicado  
→ autor recebe notificação.

---

# 11. Oportunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo principal:

Sidebar  
→ Oportunidade.

A página possui:

## Oportunidades do IFSul

Recursos institucionais confirmados.

## Oportunidades indicadas pela comunidade

Conteúdos enviados por estudantes e aprovados pela Moderação.

---

# 12. Indicar oportunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo:

Oportunidade  
→ Indicar oportunidade.

Campos:

- Título;
- Tipo;
- Descrição opcional;
- Link.

Fluxo:

Preencher  
→ Enviar para análise  
→ loading  
→ Oportunidade enviada para análise  
→ status PENDENTE.

A oportunidade não aparece imediatamente na página.

---

# 13. Moderação de oportunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo:

Oportunidade enviada  
→ PENDENTE  
→ ADMIN analisa.

## Aprovação

→ APROVADO  
→ oportunidade passa a aparecer em Oportunidade  
→ data pública considera aprovação/publicação  
→ autor recebe notificação.

## Rejeição

→ REJEITADO  
→ não é publicada  
→ autor recebe notificação  
→ motivo pode ser exibido quando informado.

---

# 14. Comunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo principal:

Sidebar  
→ Comunidade.

A Comunidade possui:

- O que está rolando;
- Encontre estudantes;
- Conecte-se com a comunidade.

---

# 15. Compartilhar com a Comunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo:

Comunidade  
→ Compartilhar com a comunidade.

Campos:

- Título;
- Descrição;
- Link.

Fluxo:

Preencher  
→ Enviar para análise  
→ loading  
→ Conteúdo enviado para análise  
→ status PENDENTE.

A publicação não aparece imediatamente no feed.

---

# 16. Moderação de publicação

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo:

Publicação enviada  
→ PENDENTE  
→ ADMIN analisa.

## Aprovação

→ APROVADO  
→ publicação aparece no feed da Comunidade  
→ data pública = data de aprovação/publicação  
→ autor recebe notificação.

## Rejeição

→ REJEITADO  
→ publicação não aparece no feed  
→ autor recebe notificação.

---

# 17. Feed da Comunidade

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

> **Atualização (08/11/2026):** o feed voltou como mural dos materiais aprovados (`decisoes.md` 68). Fluxo: Sidebar → Comunidade → mural → nome do autor (Perfil público), título (disciplina rolada até o material) ou disciplina. No material anônimo, "Comunidade" aparece sem link.

O feed segue ordem cronológica.

Para conteúdos que passaram por Moderação, a ordenação pública considera a:

**data de aprovação/publicação.**

Isso vale para conteúdos que só se tornam visíveis para os estudantes depois da aprovação.

Pode apresentar:

## Material

Exemplo:

**Novo material disponível**

→ Ver material  
→ abrir Página da disciplina em Estudos.

## Publicação

Exemplo:

**Ana Silva compartilhou**

→ Ver publicação externa.

O Nome de exibição do autor pode abrir:

→ Perfil público.

---

# 18. Encontrar estudantes

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Fluxo:

Comunidade  
→ Encontre estudantes  
→ pesquisar.

A busca pode considerar:

- Nome de exibição;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais.

O próprio usuário autenticado não aparece nos próprios resultados.

Sem busca ativa:

→ estudantes aparecem em ordem alfabética.

Filtros:

- Todos;
- Pode ajudar;
- Procura ajuda.

---

# 19. Busca por disciplina

> **Fora do MVP (02/11/2026):** `decisoes.md` 56.

Exemplo:

Usuário busca:

**Sistemas Distribuídos**

Resultado:

→ estudantes que atualmente cursam Sistemas Distribuídos.

A interface deve indicar:

**Cursa atualmente**

Isso não significa:

**Pode ajudar com Sistemas Distribuídos**

As duas informações são independentes.

---

# 20. Perfil público

> **Atualização (04/11/2026):** implementado sem o Semestre e sem lista de materiais. O acesso é pelo nome do autor no material, numa URL com código aleatório (`decisoes.md` 57).

Fluxo no MVP:

Página da disciplina  
→ material identificado  
→ clicar no Nome de exibição do autor  
→ Perfil público.

O material anônimo não dá acesso ao perfil do autor.

Pode apresentar:

- avatar;
- Nome de exibição;
- Curso;
- Sobre;
- Interesses;
- Pode ajudar com;
- Procura ajuda com;
- Disciplinas atuais;
- GitHub;
- LinkedIn.

Não apresenta:

- e-mail institucional;
- Nome completo da conta quando diferente do Nome de exibição;
- dados administrativos;
- dados de autenticação.

---

# 21. Configurações

Fluxo:

Sidebar  
→ Configurações.

A página apresenta:

## Conta

- E-mail institucional em modo somente leitura.

## Segurança

- alterar senha.

---

# 22. Alterar senha

Fluxo:

Configurações  
→ Senha atual  
→ Nova senha  
→ Confirmar nova senha  
→ Salvar nova senha  
→ validar  
→ loading  
→ sucesso.

Mensagem:

**Senha alterada com sucesso.**

Após o sucesso:

→ limpar os campos de senha.

> **Atualização (04/11/2026):** a troca encerra as outras sessões da conta, e a pessoa continua logada no aparelho atual (`decisoes.md` 61.1).

---

# 23. Notificações

O sino pode apresentar notificações relacionadas à Moderação.

Tipos principais:

- Material aprovado;
- Material rejeitado;
- Publicação aprovada;
- Publicação rejeitada;
- Oportunidade aprovada;
- Oportunidade rejeitada.

Quando houver motivo de rejeição:

→ ele pode ser exibido ao estudante.

O MVP possui:

- badge de não lidas;
- dropdown;
- marcar como lida;
- marcar todas como lidas;
- estado vazio.

> **Atualização (04/11/2026):** resolvido. Clicar marca como lida e leva ao conteúdo: uma aprovação abre a disciplina, e uma rejeição abre o item em Meus conteúdos (`decisoes.md` 55.5).

Essa decisão não bloqueia:

- o checkpoint com a professora;
- a definição da arquitetura geral;
- a implementação dos módulos anteriores.

Ela deverá estar fechada antes da implementação definitiva de Notificações.

---

# 24. Moderação

Fluxo:

ADMIN  
→ Moderação.

A fila apresenta apenas conteúdos:

**PENDENTES**

Tipos:

- Material;
- Publicação;
- Oportunidade.

Ordem:

mais antigos primeiro.

Fluxo:

Fila  
→ Analisar  
→ visualizar detalhes  
→ Aprovar ou Rejeitar.

---

# 25. ADMIN e próprio conteúdo

> **Atualização (02/11/2026):** a regra mudou. O ADMINISTRADOR pode moderar o próprio conteúdo, e o MODERADOR não vê o próprio envio na fila nem pode decidir sobre ele (`decisoes.md` 51.2, implementado na etapa 2a).

Se um ADMIN enviar um conteúdo como estudante:

→ conteúdo aparece na fila  
→ recebe indicação “Seu envio”  
→ pode ser consultado  
→ não apresenta ações Aprovar/Rejeitar.

Mensagem:

**Este conteúdo deve ser analisado por outro administrador.**

Outro ADMIN deverá realizar a análise.

---

# 26. Conteúdo já processado

Cenário:

ADMIN A e ADMIN B abrem o mesmo conteúdo.

ADMIN A decide primeiro.

ADMIN B tenta tomar outra decisão.

Resultado:

→ sistema não sobrescreve a primeira decisão  
→ apresenta:

**Este conteúdo já foi analisado.**

→ Atualizar fila.

---

# 27. Acesso não permitido

Se um ALUNO tentar acessar diretamente a rota da Moderação:

→ não mostrar conteúdo administrativo.

Apresentar:

**Você não possui permissão para acessar esta área.**

Ação:

**Voltar para a Home**

A autorização deve existir também no backend e no banco.

---

# 28. Logout

Fluxo:

Sair  
→ encerrar sessão  
→ Login.

---

# 29. Links externos

Ao acessar recursos externos:

→ abrir em nova guia.

Exemplos:

- ementas;
- GitHub;
- LinkedIn;
- oportunidades;
- publicações;
- Arena Games;
- recursos institucionais.

Links institucionais ainda não confirmados não devem funcionar como links falsos.

O levantamento dos links institucionais ainda pendentes será retomado após o checkpoint com a professora.
---

# 30. Estados gerais

As principais áreas devem considerar:

## Loading

Enquanto dados são carregados.

## Erro

Quando a operação não pode ser concluída.

## Estado vazio

Quando não existem dados.

## Busca sem resultado

Quando existem dados no módulo, mas nenhum corresponde à busca.

Esses estados devem ser diferenciados visual e semanticamente.
