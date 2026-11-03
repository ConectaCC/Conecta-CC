# Design e Protótipo — ConectaCC

Este diretório reúne as informações relacionadas à identidade visual, experiência do usuário e prototipação do **ConectaCC**.

## Protótipo navegável

O protótipo atual do ConectaCC está disponível em:

https://claude.ai/artifact/Gwv1arQoKYix47kJHF3dF4

> O protótipo representa o comportamento planejado do MVP e não significa que as funcionalidades já estejam implementadas em código.

## Telas do MVP

> **Atualização (02/11/2026):** Oportunidade e Comunidade + Perfil público foram prototipadas, mas Oportunidade e Comunidade saíram do MVP (`decisoes.md` 56). O Perfil público continua.

O protótipo foi organizado nas seguintes áreas:

1. Login
2. Cadastro + Termos de participação
3. Recuperação de senha
4. Home
5. Perfil
6. Estudos
7. Oportunidade
8. Comunidade + Perfil público
9. Configurações
10. Moderação — ADMIN

## Status atual do protótipo

### Aprovadas e congeladas

- Login
- Cadastro + Termos
- Recuperação de senha
- Home
- Perfil
- Estudos
- Oportunidade
- Comunidade + Perfil público
- Configurações

### Em finalização

- Moderação — ADMIN

Depois da conclusão da Moderação será realizada uma revisão geral da navegação e dos fluxos do protótipo.

## Layout interno

As telas autenticadas utilizam uma estrutura visual compartilhada, composta por:

- sidebar;
- header;
- sino de notificações;
- avatar com iniciais;
- nome de exibição;
- área principal de conteúdo;
- padrões compartilhados de botões, formulários, cards e estados.

A sidebar principal contém:

- Home
- Estudos
- Oportunidade
- Comunidade
- Perfil

Na parte inferior:

- Configurações
- Sair

Para usuários com papel **ADMIN**, também existe:

- Moderação

## Identidade visual

A identidade do ConectaCC busca transmitir:

- conexão;
- conhecimento;
- tecnologia;
- confiança;
- comunidade;
- colaboração;
- modernidade;
- acolhimento.

A linguagem visual utiliza principalmente:

- azul-marinho profundo;
- azuis intermediários;
- azul gelo;
- superfícies claras;
- neutros frios;
- pequenos detalhes luminosos em tons azulados.

Elementos gráficos sutis, como pontos, linhas e conexões, representam a ideia de pessoas, conhecimentos e oportunidades conectadas.

## Cores principais

Algumas das referências atualmente utilizadas no Design System são:

- `#071F43` — azul-marinho profundo;
- `#0D1933` — fundo escuro;
- `#2259B9` — ações principais;
- `#4D84CF` — foco, ícones e detalhes funcionais;
- `#A3C5F2` — destaque claro e identidade;
- `#EEF7FC` — fundo claro/gelo.

Outras cores neutras e semânticas podem ser utilizadas conforme o Design System.

## Princípios de interface

A interface deve ser:

- acadêmica;
- tecnológica;
- moderna;
- acolhedora;
- simples de compreender;
- consistente entre as diferentes áreas.

A interface não deve parecer:

- um sistema administrativo antigo;
- uma rede social genérica;
- um dashboard corporativo excessivamente carregado;
- uma interface gamer ou cyberpunk.

## Acessibilidade

O protótipo considera princípios como:

- contraste adequado;
- foco visível;
- navegação por teclado;
- labels reais em formulários;
- mensagens de erro associadas aos campos;
- estados que não dependem somente de cor;
- identificação adequada de links externos;
- feedback de loading e sucesso;
- adaptação para dispositivos móveis.

## Responsividade

No desktop, as páginas autenticadas utilizam sidebar fixa e header global.

Em telas menores, a navegação foi prototipada utilizando uma gaveta lateral.

O comportamento responsivo definitivo será refinado durante a implementação.

## Próximos passos do design

Após a conclusão da Tela de Moderação:

1. revisar todos os links entre as telas;
2. verificar consistência de nomes e componentes;
3. revisar estados de loading, erro e vazio;
4. revisar comportamento mobile;
5. revisar notificações;
6. consolidar a documentação do MVP;
7. preparar o protótipo para apoiar a implementação e os futuros testes com estudantes.
