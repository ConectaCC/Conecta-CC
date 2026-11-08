# Identidade Visual — ConectaCC

Este documento registra os princípios visuais definidos para o ConectaCC.

A identidade busca representar o propósito do projeto:

> conectar estudantes, conhecimento, oportunidades e comunidade.

O objetivo não é criar apenas uma interface visualmente agradável, mas uma linguagem que ajude a comunicar o que o ConectaCC pretende ser.

---

# 1. Conceito da identidade

O ConectaCC é uma plataforma acadêmica e colaborativa.

Sua identidade deve transmitir principalmente:

- conexão;
- conhecimento;
- tecnologia;
- confiança;
- colaboração;
- comunidade;
- modernidade;
- acolhimento.

A interface deve parecer tecnológica sem perder o caráter acadêmico e humano.

---

# 2. Ideia central

A identidade visual parte da ideia de:

**pontos que se conectam.**

Esses pontos podem representar:

- estudantes;
- conhecimentos;
- disciplinas;
- oportunidades;
- recursos institucionais;
- experiências;
- comunidade.

Linhas, arcos, pontos luminosos e pequenos elementos de conexão podem aparecer como apoio visual.

Eles devem ser sutis.

O conteúdo continua sendo o elemento principal da interface.

---

# 3. Significado das cores

O azul é a base visual do ConectaCC.

Ele foi escolhido por estar associado aos conceitos que queremos transmitir, como:

- confiança;
- conhecimento;
- estabilidade;
- tecnologia;
- comunicação;
- ambiente acadêmico.

A identidade utiliza diferentes profundidades de azul para separar funções.

---

## 3.1 Azul-marinho profundo

Referências principais:

`#071F43`

`#0D1933`

Utilização:

- fundos escuros;
- sidebar;
- áreas de identidade;
- elementos estruturais.

Significado visual:

- confiança;
- profundidade;
- estabilidade;
- tecnologia;
- ambiente acadêmico.

Esses tons criam a base mais sólida do sistema.

---

## 3.2 Azul de ação

Referência:

`#2259B9`

Utilização:

- botões primários;
- links de ação;
- elementos que precisam de maior destaque;
- ações principais.

Esse tom foi escolhido para ações porque oferece melhor contraste com texto branco do que azuis mais claros utilizados em detalhes visuais.

---

## 3.3 Azul funcional

Referência:

`#4D84CF`

Utilização:

- ícones;
- foco;
- bordas em estados ativos;
- elementos funcionais;
- detalhes de interface.

Não deve ser utilizado indiscriminadamente como fundo de botão com texto branco quando o contraste não for adequado.

---

## 3.4 Azul claro

Referência:

`#A3C5F2`

Utilização:

- detalhes luminosos;
- indicadores;
- elementos de conexão;
- destaque da identidade;
- parte "CC" da marca sobre fundos escuros.

Esse tom representa a parte mais leve e conectiva da identidade.

---

## 3.5 Azul gelo

Referência:

`#EEF7FC`

Utilização:

- fundo das páginas internas;
- superfícies claras;
- áreas de apoio;
- blocos informativos.

O objetivo é evitar uma interface totalmente branca, mantendo sensação de leveza e continuidade com a identidade azul.

---

# 4. Cores semânticas

As cores semânticas devem manter significado consistente.

## Verde

Utilizar principalmente para:

- sucesso;
- aprovação;
- operação concluída.

Exemplos:

- Perfil atualizado com sucesso;
- conteúdo aprovado;
- senha alterada com sucesso.

O verde não deve ser utilizado simplesmente para indicar que determinado conteúdo está disponível.

Por exemplo:

**Materiais disponíveis**

utiliza azul funcional, não verde.

---

## Vermelho

Utilizar para:

- erro;
- rejeição;
- ação destrutiva.

Exemplos:

- Rejeitar conteúdo;
- Descartar alterações;
- erro de validação.

O uso deve ser controlado para não transformar ações comuns em alertas desnecessários.

---

## Âmbar / atenção

Pode ser utilizado para:

- estado PENDENTE;
- avisos;
- situações que exigem atenção sem representar erro.

---

## Status e tags de conteúdo

> **Atualização (04/11/2026):** a cor de um status tem o mesmo significado em todas as telas (`decisoes.md` 58).

| Status ou tag | Cor | Significado | Tokens |
|---|---|---|---|
| PENDENTE / EM ANÁLISE / EM REVISÃO | amarelo | aguardando decisão ou análise | `warning`, `warning-bg` |
| APROVADO | verde | resultado positivo | `success`, `success-bg` |
| REJEITADO | vermelho | resultado negativo | `danger`, `danger-bg` |
| EXCLUÍDO / RETIRADO | vermelho | conteúdo retirado | `danger`, `danger-bg` |
| CANCELADO | cinza neutro | encerrado de forma voluntária pelo próprio usuário | `slate-100`, `muted-foreground` |
| EDITADO / ALTERAÇÃO | laranja | houve modificação | `change`, `change-bg` |

"Editado/Alteração" é uma indicação adicional e nunca substitui o status de Moderação. A tag sempre traz o texto: a informação não depende só da cor.

> **Atualização (04/11/2026):** esta tabela vale para a representação do status. O aviso que confirma uma ação concluída com sucesso continua verde, mesmo quando a ação é rejeitar (`decisoes.md` 62.2).

---

## Notificações

> **Atualização (04/11/2026):** o painel de Notificações usa cores próprias (`decisoes.md` 59).

- Contador do sino: `#BD5228` (token `badge`). Substitui `#C65A2E`, que ficava abaixo do contraste mínimo com o número branco (`decisoes.md` 62.1).
- Fundo da notificação não lida: `#D9E9F8` (token `unread`).
- Ícones de aprovação e rejeição: neutros, sem fundo verde ou vermelho.

---

# 5. Marca ConectaCC

A marca textual utiliza:

**ConectaCC**

Sobre fundo escuro:

- "Conecta" em tom claro;
- "CC" em `#A3C5F2`.

O objetivo é destacar a conexão entre:

**Conecta**

e

**CC — Ciência da Computação**

sem transformar a marca em dois elementos completamente separados.

---

# 6. Nome do projeto

O nome visual deve ser escrito como:

**ConectaCC**

Evitar variações na interface como:

- Conecta CC
- Conecta-CC
- conectaCC
- CONECTACC

O nome do repositório técnico pode possuir limitações ou convenções diferentes, mas nos textos e na interface a identidade oficial é:

**ConectaCC**

---

# 7. Tipografia

A tipografia definitiva ainda pode ser refinada.

A interface atual utiliza como referência uma fonte sans-serif moderna, limpa e altamente legível.

Características desejadas:

- boa leitura em telas;
- aparência moderna;
- adequada para interfaces acadêmicas;
- boa diferenciação de pesos;
- funcionamento em desktop e mobile;
- suporte adequado a caracteres em português.

**Decisão (12/10/2026):** Plus Jakarta Sans em títulos e na marca; Inter no corpo, labels e botões. Fontes auto-hospedadas (pacotes `@fontsource-variable`), sem chamada ao Google Fonts.

Não utilizar fontes excessivamente decorativas no corpo da interface.

---

# 8. Hierarquia visual

A interface deve possuir hierarquia clara.

Prioridade:

1. título da página;
2. ação necessária;
3. conteúdo principal;
4. ações secundárias;
5. informações complementares.

Evitar interfaces em que todos os elementos tenham o mesmo peso visual.

---

# 9. Telas de autenticação

Login, Cadastro e Recuperação de senha utilizam uma identidade mais expressiva.

No desktop:

- lado esquerdo escuro;
- lado direito claro;
- divisão clara entre as áreas.

## Área escura

Pode utilizar:

- azul-marinho;
- pontos de luz;
- linhas;
- arcos;
- brilho discreto;
- mensagem institucional.

## Área clara

Deve permanecer:

- limpa;
- legível;
- clara;
- focada na tarefa.

Não utilizar gradientes exagerados atravessando as duas áreas.

---

# 10. Telas internas

Depois do Login, a experiência visual deve ser mais simples.

Estrutura:

- sidebar azul-marinho;
- header;
- fundo azul gelo;
- cards claros;
- bordas suaves;
- sombras discretas.

As páginas internas devem priorizar:

**conteúdo e legibilidade.**

Elementos decorativos de conexão podem existir, mas de forma mais sutil do que nas telas de autenticação.

---

# 11. Sidebar

A sidebar funciona como principal estrutura de navegação.

Características:

- fundo azul-marinho;
- marca no topo;
- ícones consistentes;
- item ativo facilmente identificado;
- grupo inferior separado.

Menu principal:

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil

Grupo inferior:

- Configurações
- Sair

Para ADMIN:

- Moderação

---

# 12. Estado ativo

O item ativo da sidebar utiliza combinação de elementos, como:

- contraste de fundo;
- indicador lateral;
- texto em maior destaque;
- ícone em destaque.

O estado não deve depender apenas de cor.

---

# 13. Cards

Cards devem possuir:

- bordas suaves;
- bom espaçamento interno;
- hierarquia clara;
- sombras discretas quando necessárias;
- feedback de hover e foco.

Evitar:

- sombras muito pesadas;
- excesso de bordas;
- excesso de informações no mesmo card.

---

# 14. Botões

## Primário

Utilizado para a principal ação da tela.

Exemplos:

- Entrar;
- Salvar alterações;
- Enviar para análise;
- Aprovar conteúdo.

## Secundário

Utilizado para ações complementares.

## Perigo

Utilizado somente quando houver:

- rejeição;
- descarte;
- remoção;
- perda relevante.

Exemplo:

**Descartar alterações**

---

# 15. Links externos

Links que levam o usuário para fora do ConectaCC devem ser identificados.

Padrão visual:

**Nome do recurso ↗**

Exemplos:

- GitHub ↗
- LinkedIn ↗
- Arena Games ↗
- Ver oportunidade ↗

O objetivo é comunicar que o usuário será direcionado para outro ambiente.

---

# 16. Avatar

No MVP, o avatar utiliza iniciais.

Regra:

primeira letra do primeiro nome de exibição  
+  
primeira letra do último nome de exibição.

Exemplos:

Viviane Biazus Aneris → VA

Marcelo Henrique Germiniani Panho → MP

Ana Silva → AS

Upload de foto fica para evolução futura.

---

# 17. Ícones

Os ícones devem:

- possuir linguagem visual consistente;
- ser simples;
- apoiar compreensão;
- não substituir textos importantes.

Evitar misturar diversas famílias ou estilos de ícones.

O emoji 👋 é uma exceção aprovada exclusivamente para a saudação da Home.

---

# 18. Estados

A interface deve diferenciar claramente:

- padrão;
- hover;
- foco;
- loading;
- sucesso;
- erro;
- vazio;
- desabilitado;
- pendente;
- aprovado;
- rejeitado.

A informação não pode depender somente de cor.

Sempre que necessário, utilizar também:

- texto;
- ícone;
- rótulo.

---

# 19. Loading

Preferir:

**skeletons**

para carregamentos de conteúdo.

Evitar spinner grande ocupando toda a tela quando apenas parte das informações está sendo carregada.

---

# 20. Estados vazios

Estados vazios devem explicar a situação e, quando apropriado, oferecer uma ação.

Exemplo:

**Ainda não há materiais compartilhados.**

**Compartilhar material**

Não tratar ausência de dados como erro.

---

# 21. Erros

Erros devem:

- explicar o problema de forma compreensível;
- indicar o que o usuário pode fazer;
- preservar dados preenchidos quando possível;
- não utilizar linguagem excessivamente técnica.

---

# 22. Responsividade

No desktop:

- sidebar fixa;
- header;
- conteúdo estruturado.

No mobile:

- sidebar transformada em drawer;
- cards em coluna;
- formulários adaptados;
- ações com tamanho adequado para toque;
- modais e painéis adaptados à largura disponível.

A estratégia atual é uma referência do protótipo e deverá ser validada durante a implementação.

---

# 23. Acessibilidade

A identidade visual deve considerar:

- contraste adequado;
- foco visível;
- labels;
- navegação por teclado;
- estados que não dependem apenas de cor;
- mensagens de erro compreensíveis;
- links externos identificados;
- hierarquia correta de títulos;
- suporte a tecnologias assistivas.

A acessibilidade deve ser tratada como parte da experiência, não como ajuste posterior.

---

# 24. O que a interface deve transmitir

O ConectaCC deve parecer:

- acadêmico;
- tecnológico;
- moderno;
- confiável;
- colaborativo;
- organizado;
- acessível;
- acolhedor.

---

# 25. O que evitar

Evitar uma aparência:

- excessivamente corporativa;
- antiga;
- burocrática;
- visualmente poluída;
- gamer;
- cyberpunk;
- excessivamente neon;
- semelhante a uma rede social genérica.

O ConectaCC deve preservar sua identidade acadêmica e comunitária.

---

# 26. Princípio final

A identidade visual deve apoiar a ideia central do projeto:

> O ConectaCC conecta pessoas, conhecimentos, oportunidades e recursos sem competir com aquilo que já existe.

A interface deve facilitar essa conexão de forma clara, confiável e acolhedora.

---

> **Atualização (08/11/2026):** nas páginas internas, o fundo passa do branco do cabeçalho para o azul-claro da página (`--color-ice`) por um degradê vertical bem discreto, nos primeiros 140 px da área principal. Depois disso, a página segue no mesmo azul-claro. O degradê fica no `AppLayout` e vale para todas as páginas que usam esse layout. Não há nova cor, faixa nem sombra.
