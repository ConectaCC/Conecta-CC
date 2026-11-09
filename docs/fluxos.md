# Fluxos do ConectaCC

Este documento descreve os principais fluxos funcionais previstos para o MVP do ConectaCC.

Os fluxos representam o comportamento esperado do produto e servem como referência para:

- prototipação;
- implementação;
- testes;
- validação com usuários.


> **Escopo atual:** Estudos, Oportunidade, Comunidade, Perfil, Moderação, Meus conteúdos e Notificações fazem parte da aplicação atual. Funcionalidades futuras são identificadas ao longo deste documento.
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

Durante o período de teste fechado:

- o cadastro fica restrito aos e-mails convidados;
- o e-mail ainda precisa pertencer ao domínio institucional permitido;
- quem não estiver na lista de convidados recebe a informação de que o cadastro está restrito aos participantes convidados.
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

A recuperação de senha não faz parte da versão atual.

Caso seja implementada futuramente, deverá utilizar um fluxo seguro:

Login  
→ Esqueci minha senha  
→ informar E-mail institucional  
→ receber instruções por e-mail  
→ acessar link de recuperação  
→ definir nova senha  
→ confirmar nova senha  
→ Senha redefinida  
→ Login.

A resposta inicial não deverá confirmar se existe uma conta associada ao e-mail informado.

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
→ escolher entre **Materiais** e **Oportunidades**.

Cada tipo possui filtros por situação, como:

- Todos;
- Em andamento;
- Aprovados;
- Rejeitados;
- Excluídos.

### Materiais

Em análise  
→ Cancelar envio  
→ confirmar  
→ CANCELADO  
→ sai da fila.

Aprovado  
→ Editar  
→ Enviar para análise  
→ alteração PENDENTE  
→ a versão publicada continua disponível até a aprovação da alteração.

Aprovado  
→ Excluir  
→ confirmar  
→ deixa de aparecer em Estudos e na Comunidade  
→ continua consultável em Meus conteúdos como Excluído.

Rejeitado  
→ consultar motivo  
→ para corrigir, realizar um novo envio.

### Oportunidades

Em análise  
→ Cancelar envio  
→ confirmar  
→ CANCELADO.

Aprovada  
→ pode ser excluída pelo autor.

Oportunidades não possuem edição na versão atual.

As listas utilizam **Mostrar mais** quando necessário.

---

# 10. Moderação de material

Fluxo:

Material enviado  
→ PENDENTE  
→ MODERADOR ou ADMINISTRADOR acessa Moderação  
→ abre conteúdo  
→ analisa.

O MODERADOR não recebe na própria fila materiais enviados por ele.

O ADMINISTRADOR pode moderar o próprio conteúdo.

## Se aprovado

→ Aprovar  
→ confirmar aprovação  
→ APROVADO  
→ material sai da fila  
→ aparece em Estudos e na Comunidade  
→ data pública = data de aprovação/publicação  
→ autor recebe notificação.

Quando identificado, o material mostra o Nome de exibição do autor.

Quando enviado anonimamente:

→ aparece como **Compartilhado pela comunidade** nas áreas públicas.

## Se rejeitado

→ Rejeitar  
→ informar motivo obrigatório  
→ confirmar rejeição  
→ REJEITADO  
→ material sai da fila  
→ não é publicado  
→ autor recebe notificação  
→ motivo fica disponível em Meus conteúdos.
---

# 11. Oportunidade

Fluxo principal:

Sidebar  
→ Oportunidade.

A página apresenta:

- acessos para oportunidades oficiais do IFSul;
- oportunidades indicadas pela comunidade;
- busca;
- filtro por tipo;
- opção de adicionar oportunidade.

Ao selecionar uma oportunidade:

→ acessar o link original em nova guia.

---

# 12. Indicar oportunidade

Fluxo:

Oportunidade  
→ Adicionar oportunidade.

ou:

Comunidade  
→ Compartilhar conteúdo  
→ Oportunidade.

Campos:

- Tipo;
- Título;
- Descrição curta;
- Link;
- Modalidade;
- Inscrições até, opcional.

Tipos:

- Estágio;
- Emprego;
- Evento;
- Projeto;
- Outro.

Modalidades:

- Remoto;
- Híbrido;
- Presencial.

Fluxo:

Preencher  
→ Enviar para análise  
→ Oportunidade enviada para análise  
→ PENDENTE  
→ acompanhar em Meus conteúdos.

Sem data de encerramento informada, a oportunidade permanece visível por 15 dias após a aprovação.

---

# 13. Moderação de oportunidade

Fluxo:

Oportunidade enviada  
→ PENDENTE  
→ MODERADOR ou ADMINISTRADOR acessa Moderação  
→ analisa.

O MODERADOR não pode analisar a própria oportunidade.

O ADMINISTRADOR pode moderar o próprio envio.

## Aprovação

→ APROVADO  
→ sai da fila  
→ aparece em Oportunidade e no mural da Comunidade  
→ autor recebe notificação.

## Rejeição

→ informar motivo obrigatório  
→ REJEITADO  
→ sai da fila  
→ não é publicada  
→ autor recebe notificação  
→ motivo fica disponível em Meus conteúdos.
---

# 14. Comunidade

Fluxo principal:

Sidebar  
→ Comunidade.

A Comunidade funciona atualmente como um mural de descoberta dos conteúdos compartilhados no ConectaCC.

O mural reúne:

- materiais aprovados;
- oportunidades aprovadas.

A página permite:

- buscar conteúdos;
- filtrar por tipo;
- ordenar resultados;
- alternar entre visualização em lista e grade;
- carregar mais resultados;
- acessar os conteúdos;
- utilizar o menu Compartilhar conteúdo.

Funcionalidades como busca de estudantes, canais e publicações externas ficam para evoluções futuras.

---

# 15. Compartilhar com a Comunidade

> **Evolução futura:** este fluxo não faz parte da versão atual.

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

> **Evolução futura:** este fluxo não faz parte da versão atual.

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

# 17. Mural da Comunidade

Fluxo:

Sidebar  
→ Comunidade  
→ mural.

O mural apresenta materiais e oportunidades aprovados, do mais recente para o mais antigo por padrão.

O usuário pode:

- buscar;
- filtrar por tipo;
- alterar a ordem;
- alternar entre lista e grade;
- carregar mais resultados.

## Material

Material identificado:

→ mostra o Nome de exibição do autor  
→ nome pode abrir o Perfil público.

Material anônimo:

→ aparece como compartilhado pela Comunidade  
→ não revela o autor.

Ao selecionar o título do material:

→ abrir a disciplina correspondente em Estudos  
→ localizar o material.

## Oportunidade

→ aparece como adicionada pela Comunidade  
→ não mostra publicamente quem realizou o envio.

Ao acessar:

→ abrir a oportunidade correspondente.

Materiais excluídos e oportunidades encerradas ou excluídas deixam de aparecer no mural.

---

# 18. Encontrar estudantes

> **Evolução futura:** a busca de estudantes ainda não faz parte da versão atual.

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

> **Evolução futura:** a busca de estudantes ainda não faz parte da versão atual.

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

O sino apresenta notificações relacionadas à análise dos conteúdos enviados.

Entre os eventos atuais estão:

- material aprovado;
- material rejeitado;
- alteração de material aprovada;
- alteração de material rejeitada;
- oportunidade aprovada;
- oportunidade rejeitada.

O sistema possui:

- contador de não lidas;
- painel de notificações;
- indicação de notificação nova;
- marcar como lida;
- marcar todas como lidas.

Ao clicar em uma notificação:

→ ela é marcada como lida  
→ o usuário é direcionado ao destino correspondente.

Exemplos:

Aprovação de material  
→ disciplina correspondente.

Rejeição de material  
→ item correspondente em Meus conteúdos.

Aprovação de oportunidade  
→ oportunidade correspondente.

Rejeição de oportunidade  
→ item correspondente em Meus conteúdos.

O motivo da rejeição pode ser consultado pelo autor em Meus conteúdos.
---

# 24. Moderação

Fluxo:

MODERADOR ou ADMINISTRADOR  
→ Moderação.

A fila apresenta conteúdos:

**PENDENTES**

Tipos atuais:

- Material;
- Oportunidade.

A fila é única e não possui filtro por tipo.

Ordem:

**mais antigos primeiro.**

Fluxo:

Fila  
→ Analisar  
→ visualizar detalhes  
→ Aprovar ou Rejeitar.

A rejeição exige motivo.

---

# 25. Moderação do próprio conteúdo

## MODERADOR

O próprio envio não aparece na fila de Moderação do MODERADOR.

Ele deverá ser analisado por outro MODERADOR ou por um ADMINISTRADOR.

## ADMINISTRADOR

O ADMINISTRADOR pode moderar conteúdos enviados pela própria conta.

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

Links institucionais pendentes só devem ser disponibilizados depois da confirmação do endereço correto.
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
