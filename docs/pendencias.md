# Pendências e decisões em aberto — ConectaCC

Este documento registra decisões que ainda precisam ser tomadas, informações que precisam ser confirmadas e questões que deverão ser resolvidas durante a implementação ou antes do piloto do ConectaCC.

A existência de um item nesta lista não significa que ele deveria estar concluído neste momento.

O objetivo é diferenciar claramente:

- decisões de produto ainda abertas;
- pendências técnicas;
- pendências institucionais;
- requisitos obrigatórios antes do piloto;
- possibilidades de evolução pós-MVP.

---

# 1. Decisões de produto ainda abertas

# 1. Decisões e próximos passos de produto

Depois da auditoria final do protótipo, algumas questões que estavam em aberto já foram decididas pela equipe.

Esta seção mantém apenas decisões que ainda precisam ser tomadas ou atividades de produto que deverão ser retomadas no momento adequado.

---

## 1.1 Informações e Serviços

Ainda precisa ser definido onde o ConectaCC reunirá ou contextualizará acessos como:

- SUAP;
- Moodle;
- Painel de Sistemas do Câmpus;
- Instituto de Informática;
- professores;
- organograma;
- ementas;
- outros serviços e informações institucionais.

Esse conteúdo fazia parte da concepção original do ConectaCC, mas não aparece atualmente como módulo independente na navegação do MVP.

A decisão deverá considerar o princípio:

> O ConectaCC deve conectar e contextualizar recursos existentes, e não reconstruir sistemas institucionais.

Antes da decisão definitiva, será realizado um levantamento mais completo dos recursos institucionais disponíveis.

**Quando retomar:** após o checkpoint com a professora.

A decisão deverá estar fechada antes da implementação das áreas afetadas.

---

## 1.2 Comportamento das notificações

> **Atualização (02/11/2026):** resolvido (`decisoes.md` 55.5).

Ainda deve ser definido o comportamento ao clicar em uma notificação.

Questões:

- abrir uma notificação deve marcá-la automaticamente como lida?
- a notificação deve levar diretamente ao conteúdo correspondente?
- uma notificação de material aprovado deve abrir a disciplina?
- uma notificação de oportunidade aprovada deve abrir Oportunidade?
- uma publicação aprovada deve abrir a Comunidade?

Essa decisão não precisa ser tomada antes da definição da arquitetura geral.

**Quando decidir:** antes da implementação definitiva de Notificações.

---

## 1.3 Catálogo acadêmico e disciplinas piloto

A fonte institucional inicial para o catálogo acadêmico já foi identificada.

### Matriz 2023

Grade Curricular do Bacharelado em Ciência da Computação:

https://inf.passofundo.ifsul.edu.br/src/bcc_grade/index.html

Matriz / Organograma de pré-requisitos:

https://inf.passofundo.ifsul.edu.br/src/bcc_organograma/index.html

A Matriz 2023 será utilizada como referência acadêmica inicial do MVP.

Ela organiza o curso do:

**1º ao 8º semestre.**

Também foram identificadas fontes institucionais para:

- disciplinas;
- carga horária;
- ementas;
- organização por semestre;
- pré-requisitos.

Ainda precisam ser definidas:

- quais disciplinas terão área de materiais inicialmente;
- quais disciplinas participarão do piloto.

A necessidade de contemplar estudantes ainda vinculados à Matriz 2017 será verificada posteriormente, quando esse levantamento se tornar necessário para preparar os dados reais do piloto.

Essa verificação não bloqueia a arquitetura nem as atividades atuais.

> **Atualização (19/10/2026):** o catálogo já está carregado com as 49 disciplinas obrigatórias da Matriz 2023 (`decisoes.md` 53.2). Continuam em aberto: quais disciplinas terão área de materiais, quais participarão do piloto, as eletivas e a Matriz 2017.

> **Atualização (31/10/2026):** todas as disciplinas têm área de materiais (`decisoes.md` 54.2), então não há mais escolha de disciplinas piloto para materiais. Continuam em aberto: as disciplinas do piloto com estudantes, as eletivas e a Matriz 2017.

---

## 1.4 Anonimato público dos materiais

> **Atualização (02/11/2026):** resolvido antes do previsto. O material mostra o nome do autor, com a opção "Compartilhar anonimamente" (`decisoes.md` 55.2).

No MVP atual, materiais aparecem como:

**Compartilhado pela comunidade**

O ADMIN continua identificando o autor para fins de Moderação e notificação.

A decisão sobre apresentar ou não a autoria publicamente não será tomada neste semestre.

**Quando decidir:** no próximo semestre, após os primeiros testes com estudantes.

Até lá, permanece o comportamento atual do MVP:

**Compartilhado pela comunidade**

---

# 1.5 Decisões fechadas após a auditoria

As seguintes questões estavam em aberto no momento da auditoria, mas já foram decididas pela equipe.

## Data pública de materiais e publicações

A data pública utilizada será:

**data de aprovação/publicação.**

A mesma regra será utilizada para:

- Material;
- Publicação;
- Oportunidade.

Exemplo:

Conteúdo enviado na segunda-feira  
→ permanece PENDENTE  
→ ADMIN aprova na quarta-feira  
→ publicamente aparece como publicado na quarta-feira.

A data original do envio poderá permanecer registrada internamente.

---

## E-mail utilizado no MVP e no piloto

O cadastro do MVP e do piloto utilizará:

**e-mail institucional do IFSul.**

A possibilidade de aceitar e-mails institucionais de outras instituições será considerada apenas caso exista uma futura expansão do ConectaCC para fora do IFSul.

A confirmação técnica do domínio ou dos domínios exatos utilizados pelo IFSul poderá ser realizada durante a implementação.

> **Domínio adotado (12/10/2026):** `academico.ifsul.edu.br`, configurável por `ALLOWED_EMAIL_DOMAINS`.

Isso não altera a regra de produto:

**MVP e piloto = e-mail institucional do IFSul.**

---

## Confirmação de propriedade do e-mail

No MVP e no piloto:

**não haverá confirmação de cadastro por link ou código enviado ao e-mail.**

Essa funcionalidade poderá ser avaliada em uma evolução futura do ConectaCC.

Essa decisão não altera a recuperação de senha, que continuará dependendo de envio de e-mail.

---

## E-mails utilizados nos exemplos

Todos os exemplos apresentados no ConectaCC deverão representar:

**e-mail institucional do IFSul.**

Exemplos genéricos como:

`viviane@exemplo.com`

não deverão permanecer na versão final.

A padronização visual poderá ser realizada durante a limpeza final do protótipo.

---

# 1.6 Visibilidade do Perfil (evolução futura)

> Registrado em 04/11/2026. Não faz parte do MVP e não bloqueia o piloto (`decisoes.md` 57).

## Ocultar as disciplinas atuais

Antes de implementar, definir:

- quem esconde as próprias disciplinas pode continuar vendo as dos outros?
- pode continuar procurando estudantes por disciplina?
- continua aparecendo em resultados relacionados àquela disciplina?
- poderá esconder todas ou apenas algumas?

## Perfil oculto ou anônimo

Antes de implementar, definir:

- se o Perfil inteiro pode ser ocultado ou apenas campos específicos;
- quem ainda poderá acessá-lo;
- o que acontece com conteúdos identificados já publicados;
- como ficam busca e descoberta de estudantes;
- como ficam "Posso ajudar com" e "Procuro ajuda com";
- quais funcionalidades o usuário perde se escolher não participar da descoberta social.

## Semestre no Perfil

Será tratado junto dessas regras de visibilidade.

---

# 2. Pendências técnicas de implementação

Estas decisões serão tratadas principalmente durante a definição da arquitetura e implementação.

## 2.1 Stack, banco e hospedagem

Definir:

- stack;
- arquitetura;
- frontend;
- backend;
- banco de dados;
- hospedagem;
- deploy;
- provedor de e-mail.

---

## 2.2 Recuperação de senha

Definir:

- formato do link de recuperação;
- validade do link;
- token;
- segurança;
- provedor responsável pelo envio.

---

## 2.3 Autorização ALUNO e ADMIN

> **Atualização (04/11/2026):** resolvido: enum `Role` com permissões explícitas, Gate `access-moderation` e `MaterialPolicy`, tudo no backend.

Garantir tecnicamente que:

- somente ADMIN consulte conteúdos pendentes;
- somente ADMIN aprove ou rejeite conteúdos;
- ALUNO não consiga acessar Moderação através de rota direta;
- ADMIN não consiga moderar o próprio conteúdo.

Essa proteção deve existir no backend e no banco, e não apenas na interface.

---

## 2.4 Concorrência na Moderação

> **Atualização (04/11/2026):** resolvido: a decisão é um `UPDATE ... WHERE status = 'PENDENTE'`. A segunda tentativa recebe 409 e não altera nada.

Caso dois administradores analisem o mesmo conteúdo:

- a primeira decisão válida deve prevalecer;
- a segunda tentativa não pode sobrescrever a primeira.

O sistema deverá apresentar o estado:

**Este conteúdo já foi analisado.**

---

## 2.5 Sessões após alteração ou recuperação de senha

> **Atualização (04/11/2026):** continua pendente. Precisa ser decidido na entrega de Configurações.

Definir se outras sessões ativas serão:

- mantidas;
- encerradas.

A decisão deve contemplar:

- alteração autenticada de senha;
- recuperação de senha.

> **Continua aberta (12/10/2026):** a duração da sessão foi definida (7 dias sem uso), mas o comportamento das outras sessões após trocar a senha será decidido na etapa de Configurações.

---

## 2.6 Tentativas de Login

Definir futuramente:

- existência ou não de limite de tentativas;
- bloqueio temporário;
- atraso progressivo;
- outras medidas de segurança.

Não existe regra definitiva no protótipo.

> **Resolvida (12/10/2026):** 5 tentativas por minuto por e-mail + IP, sem bloqueio de conta. Ver `implementacao.md` §4.

---

## 2.7 Limites de texto

> **Atualização (02/11/2026):** os limites de oportunidade e de publicação não bloqueiam mais o MVP, porque essas áreas saíram dele (`decisoes.md` 56).

Ainda precisam ser definidos limites para campos como:

- Nome de exibição;
- Sobre mim;
- quantidade de tags;
- tamanho das tags;
- título de material;
- descrição de material;
- título de oportunidade;
- descrição de oportunidade;
- título de publicação;
- descrição de publicação;
- motivo da rejeição.

Os limites deverão considerar:

- banco de dados;
- segurança;
- experiência do usuário;
- apresentação dos cards.

> **Teto provisório (19/10/2026):** até a definição, o servidor recusa Nome de exibição acima de 120 caracteres, Sobre mim acima de 5.000, tags acima de 255 caracteres e mais de 50 tags por campo. São só limites de segurança, sem contador na tela, e ficam em `UpdateProfileRequest` (constantes `MAX_*`).

> **Atualização (02/11/2026):** os limites de material foram definidos: título até 200 caracteres, descrição até 2.000, link até 2.048 e motivo da rejeição até 500 (`decisoes.md` 55.3 e 55.4).

---

## 2.8 Quantidade inicial antes de “Mostrar mais”

> **Atualização (02/11/2026):** feed e lista de estudantes saíram do MVP (`decisoes.md` 56). Esta pendência não bloqueia mais o MVP.

Definir quantos itens serão exibidos inicialmente em:

- feed da Comunidade;
- lista de estudantes.

Os números atualmente utilizados no protótipo são apenas demonstrativos.

---

## 2.9 Registro versionado dos Termos

O sistema deverá registrar:

- versão do termo aceita;
- usuário;
- data/hora do aceite.

O aceite só deve ser registrado quando a conta for efetivamente criada.

---

## 2.10 Validação de URLs

> **Atualização (04/11/2026):** resolvido também para links de material: `MaterialUrl` aceita só http e https, com host válido, e normaliza porta padrão, barra final e domínios com acento (`decisoes.md` 55.6).

Na implementação, validar no servidor:

- apenas HTTP e HTTPS;
- normalização adequada para HTTPS;
- links GitHub correspondentes ao domínio GitHub;
- links LinkedIn correspondentes ao domínio LinkedIn.

Não confiar somente na validação do frontend.

> **Resolvida (19/10/2026) para GitHub e LinkedIn:** validação no servidor, só endereço de perfil, salvo como `https://` (`decisoes.md` 53.3). Os links de materiais e oportunidades seguirão a mesma regra de http/https quando forem implementados.

---

## 2.11 Foco e alterações não salvas

Implementar corretamente:

- foco preso em modais e painéis;
- retorno de foco;
- navegação por teclado;
- aviso ao fechar a aba quando houver alterações não salvas, quando aplicável.

O protótipo demonstra esses comportamentos apenas parcialmente.

---

## 2.12 Loading e erro

Completar na implementação os estados de loading e erro nas páginas que reutilizam padrões existentes.

Exemplos:

- Perfil;
- Configurações;
- Página da disciplina.

---

## 2.13 Limitações específicas do protótipo

Não tratar como regras do produto:

- persistência entre telas através do armazenamento do navegador;
- papel ADMIN não carregado automaticamente entre todos os artboards;
- dados fictícios utilizados para demonstrar estados.

Essas limitações existem somente na prototipação.

---

# 3. Pendências obrigatórias antes do piloto

## 3.1 Revisar os Termos de participação

Antes do piloto real, revisar o texto para incluir explicitamente:

- disciplinas atuais visíveis para estudantes autenticados;
- tratamento de dados;
- finalidade dos dados;
- informações relevantes sobre o período de testes.

---

## 3.2 Exclusão de conta e dados

Definir:

- como solicitar exclusão;
- quem é responsável pelo processo;
- quais dados serão removidos;
- retenção de dados;
- impacto sobre materiais enviados;
- impacto sobre publicações;
- impacto sobre oportunidades indicadas.

Essa funcionalidade não possui tela automática no MVP atual.

---

## 3.3 Quantidade de administradores

> **Atualização (02/11/2026):** o ADMINISTRADOR pode moderar o próprio conteúdo (§51.2), mas o MODERADOR não. Ainda é preciso ter mais de um MODERADOR, ou um ADMINISTRADOR, para analisar o envio de um MODERADOR.

Se usuários ADMIN também participarem do piloto como estudantes, deverá existir mais de um ADMIN.

Isso é necessário porque:

**um ADMIN não pode moderar o próprio conteúdo.**

---

## 3.4 Orientação para motivo de rejeição

> **Atualização (02/11/2026):** o motivo é obrigatório para MODERADOR e ADMINISTRADOR, com até 500 caracteres (`decisoes.md` 55.4). A orientação de redação continua pendente.

Antes do piloto, criar orientação simples para administradores.

O motivo deve ser:

- objetivo;
- compreensível;
- explicativo quando necessário;
- respeitoso;
- sem informações inadequadas ou desnecessárias.

O limite de caracteres será definido durante a implementação.

---

# 4. Recursos institucionais a mapear

Ainda deverá ser realizado um levantamento mais completo de recursos relevantes.

Entre eles:

- [x] SUAP
- [x] Moodle
- [x] Painel de Sistemas do Câmpus
- [x] site do Instituto de Informática
- [ ] professores
- [x] Matriz / Organograma de pré-requisitos
- [x] Grade Curricular
- [x] fonte institucional para ementas
- [ ] Estágios
- [ ] Projetos de Extensão
- [ ] Instagram institucional
- [ ] grupo de WhatsApp dos estudantes
- [x] Arena Games
- [ ] outros recursos identificados durante o levantamento

Para cada recurso deverá ser registrado:

- nome;
- finalidade;
- URL;
- fonte;
- área do ConectaCC em que será utilizado.

---

# 5. Preparação da validação

Antes dos testes com estudantes:

- [ ] escolher disciplinas piloto;
- [ ] definir participantes;
- [ ] revisar Termos;
- [ ] concluir levantamento dos links institucionais necessários;
- [x] confirmar o domínio técnico utilizado para validar e-mails institucionais do IFSul (`academico.ifsul.edu.br`);
- [ ] preparar formulário ou instrumento de feedback;
- [ ] definir o que será observado durante os testes;
- [ ] preparar ambiente funcional;
- [ ] definir administradores do piloto.

---

# 6. Pós-MVP

Itens já identificados como possíveis evoluções e que não fazem parte da primeira versão:

- prevenção automática de conteúdo duplicado;
- aviso de link já compartilhado;
- remoção de conteúdo já aprovado;
- histórico administrativo;
- auditoria de decisões;
- contador de pendências na Moderação;
- controle de visibilidade do Perfil;
- opção de não aparecer na Comunidade;
- pedido de ajuda;
- matching entre estudantes;
- chat;
- mensagens diretas;
- upload de arquivos;
- upload de foto;
- comentários;
- curtidas;
- reações;
- seguidores;
- recomendações;
- Inteligência Artificial;
- confirmação de propriedade do e-mail institucional por link ou código;
- gamificação.
---

# 7. Observação

A auditoria final do protótipo identificou que as áreas principais estão consistentes para servir como referência de implementação.

As pendências registradas neste documento representam principalmente:

- decisões ainda não necessárias para o protótipo;
- informações institucionais ainda não confirmadas;
- escolhas técnicas da implementação;
- requisitos necessários antes do piloto.

Portanto, elas não devem ser interpretadas como falhas ou esquecimentos do projeto.
