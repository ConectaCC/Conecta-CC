# Perfil público — design

Data: 04/11/2026
Responsável: Marcelo Henrique Germiniani Panho
Base: `main` (após o PR #8)

Referência visual: protótipo, artboards `PerfilPublico` e `PP-*`.

## 1. Intenção

"Perfil público" significa **visível para outros usuários autenticados no ConectaCC**, não para a internet. É a página em que um estudante conhece o autor de um material.

## 2. Escopo

### Entra

- Página `/estudantes/<código>`, com avatar pelas iniciais, Nome de exibição, Curso (Ciência da Computação), Disciplinas atuais, Sobre, Interesses, "Pode ajudar com", "Procura ajuda com", GitHub e LinkedIn.
- Campos opcionais vazios são omitidos (RF-57).
- No card do material identificado, o nome do autor vira link para o perfil.
- Código público aleatório por usuário.

### Não entra

- Lista de materiais no perfil.
- "Ver como outros estudantes veem meu perfil": o usuário vê e edita o próprio Perfil normalmente.
- Semestre: será tratado junto das regras de visibilidade.
- Controle de visibilidade e Perfil oculto ou anônimo (ver `pendencias.md` 1.6).

### Nunca aparece

E-mail institucional, Nome completo da conta, dados de autenticação, papel e dados administrativos, e Meus conteúdos ou qualquer histórico privado.

## 3. Decisões (04/11/2026)

1. **Acesso:** material identificado → clicar no Nome de exibição → Perfil público. O material anônimo continua com "Compartilhado pela comunidade", sem link, e a API não envia o código do autor anônimo.
2. **Código público aleatório e não sequencial** na URL, por causa da evolução futura para perfil oculto. O código **não substitui autorização**: hoje a rota exige login e, se um perfil puder ser ocultado no futuro, conhecer a URL não pode dar acesso a ele.
3. **Navegação:** o link "Voltar" retorna à página anterior, ou a Estudos quando o perfil foi aberto direto pelo endereço.

## 4. Backend

- **Coluna `users.public_code`** (`string(16)`, única, não nula), gerada pelo banco: `left(replace(gen_random_uuid()::text, '-', ''), 16)`. O valor padrão da coluna cobre os usuários existentes e os novos, inclusive os do seeder, que roda sem eventos de model. Não é fillable.
- **`GET /api/v1/students/{código}`**, dentro de `auth:sanctum`. Código em formato inválido ou inexistente dá 404. O id interno não funciona na rota.
- **`StudentProfileResource`** é uma lista fechada de campos: `display_name`, `initials`, `about`, `interests`, `can_help_with`, `needs_help_with`, `github_url`, `linkedin_url` e `disciplines`.
- **`MaterialResource`** ganha `author_code`, nulo quando o material é anônimo. A Moderação não recebe o código.

## 5. Frontend

- Rota `/estudantes/:code`, em `features/students/`.
- **Textos:**
  - "Voltar";
  - "Carregando perfil…";
  - "Não foi possível carregar este perfil agora." com "Tentar novamente";
  - "Este perfil não está disponível." com "Voltar para Estudos";
  - "Ciência da Computação";
  - "Sobre", "Disciplinas atuais", "Interesses", "Apoio entre estudantes", "Pode ajudar com", "Procura ajuda com", "GitHub" e "LinkedIn";
  - "Perfil visível apenas para estudantes autenticados no ConectaCC.".
- **Links externos:** abrem em nova guia, com "(abre em nova guia)" para leitor de tela.
- **O próprio nome num material** leva a `/perfil`, onde a pessoa vê e edita o próprio Perfil.

## 6. Testes

- **Pest:**
  - o JSON tem exatamente os campos permitidos, e e-mail, nome completo e papel não aparecem;
  - código desconhecido, em formato inválido ou o id interno dão 404;
  - sem sessão dá 401;
  - os códigos são distintos e não sequenciais, e o Perfil não aceita alterar `public_code`;
  - material anônimo não expõe o código, na lista pública nem na Moderação.
- **Playwright fora do repositório:** o link do autor, o material anônimo sem link, os campos vazios omitidos, os estados de erro e de perfil indisponível, o "Voltar" e a tela de 360 px.
