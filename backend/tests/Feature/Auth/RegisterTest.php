<?php

use App\Actions\RegisterUser;
use App\Enums\Role;
use App\Models\TermsAcceptance;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

function registerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ana Silva',
        'email' => 'ana.silva@academico.ifsul.edu.br',
        'password' => 'segredo123',
        'password_confirmation' => 'segredo123',
        'terms_version' => config('conectacc.terms_version'),
    ], $overrides);
}

it('cria a conta como ALUNO, com Nome de exibição inicial e aceite registrado', function () {
    $this->postJson('/api/v1/register', registerPayload())->assertCreated()->assertExactJson(['message' => 'Conta criada.']);

    $user = User::sole();
    expect($user->role)->toBe(Role::Aluno)
        ->and($user->display_name)->toBe('Ana Silva')
        ->and($user->email)->toBe('ana.silva@academico.ifsul.edu.br');

    $acceptance = $user->termsAcceptances()->sole();
    expect($acceptance->version)->toBe(config('conectacc.terms_version'))
        ->and($acceptance->accepted_at)->toBeInstanceOf(CarbonInterface::class);

    $this->assertGuest('web'); // o fluxo volta ao Login, sem login automático
});

it('grava o e-mail em minúsculas e sem espaços', function () {
    $this->postJson('/api/v1/register', registerPayload(['email' => '  Ana.Silva@Academico.IFSUL.edu.br ']))
        ->assertCreated();

    expect(User::sole()->email)->toBe('ana.silva@academico.ifsul.edu.br');
});

it('recusa e-mail fora do domínio institucional', function (string $email) {
    $this->postJson('/api/v1/register', registerPayload(['email' => $email]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'Use seu e-mail institucional do IFSul.']);
})->with([
    'gmail' => 'ana@gmail.com',
    'domínio-mãe' => 'ana@ifsul.edu.br',
    'subdomínio' => 'ana@sub.academico.ifsul.edu.br',
    'sufixo malicioso' => 'ana@academico.ifsul.edu.br.evil.com',
]);

it('recusa e-mail já cadastrado, mesmo com maiúsculas diferentes', function () {
    User::factory()->create(['email' => 'ana.silva@academico.ifsul.edu.br']);

    $this->postJson('/api/v1/register', registerPayload(['email' => 'ANA.SILVA@academico.ifsul.edu.br']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.']);
});

it('valida os campos com as mensagens do protótipo', function (array $override, string $field, string|array $message) {
    $this->postJson('/api/v1/register', registerPayload($override))
        ->assertUnprocessable()
        ->assertJsonPath("errors.{$field}", (array) $message);
})->with([
    'nome vazio' => [['name' => ''], 'name', 'Informe seu nome completo.'],
    'nome só com espaços' => [['name' => '    '], 'name', 'Informe seu nome completo.'],
    'nome longo' => [['name' => str_repeat('a', 121)], 'name', 'Use no máximo 120 caracteres.'],
    'nome como lista' => [['name' => ['Ana']], 'name', 'O valor enviado precisa ser um texto.'],
    'e-mail incompleto' => [['email' => 'ana.silva@'], 'email', 'Informe um endereço de e-mail válido.'],
    'e-mail vazio' => [['email' => ''], 'email', 'Informe um endereço de e-mail válido.'],
    'senha vazia' => [['password' => '', 'password_confirmation' => ''], 'password', 'Use pelo menos 8 caracteres.'],
    'senha como lista' => [['password' => ['segredo123']], 'password', ['O valor enviado precisa ser um texto.', 'Use pelo menos 8 caracteres.']],
    'senha curta' => [['password' => 'abc', 'password_confirmation' => 'abc'], 'password', 'Use pelo menos 8 caracteres.'],
    'confirmação vazia' => [['password_confirmation' => ''], 'password_confirmation', 'As senhas informadas não coincidem.'],
    'confirmação diferente' => [['password_confirmation' => 'outra-senha'], 'password_confirmation', 'As senhas informadas não coincidem.'],
    'sem Termos' => [['terms_version' => null], 'terms_version', 'Os Termos de participação foram atualizados. Leia e aceite novamente.'],
    'Termos de outra versão' => [['terms_version' => '0.0'], 'terms_version', 'Os Termos de participação foram atualizados. Leia e aceite novamente.'],
]);

it('exige os campos mesmo quando não vêm no corpo', function () {
    $this->postJson('/api/v1/register', [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.name', ['Informe seu nome completo.'])
        ->assertJsonPath('errors.email', ['Informe um endereço de e-mail válido.'])
        ->assertJsonPath('errors.password', ['Use pelo menos 8 caracteres.'])
        ->assertJsonPath('errors.password_confirmation', ['As senhas informadas não coincidem.'])
        ->assertJsonPath('errors.terms_version', ['Os Termos de participação foram atualizados. Leia e aceite novamente.']);
});

it('mostra o e-mail já cadastrado junto com os outros erros', function () {
    User::factory()->create(['email' => 'ana.silva@academico.ifsul.edu.br']);

    $this->postJson('/api/v1/register', registerPayload(['name' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email' => 'Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.']);
});

it('não deixa usuário gravado quando o aceite falha', function () {
    TermsAcceptance::creating(fn () => throw new RuntimeException('falha simulada'));
    $this->withoutExceptionHandling();

    expect(fn () => $this->postJson('/api/v1/register', registerPayload()))->toThrow(RuntimeException::class);
    expect(User::count())->toBe(0);
});

it('transforma a corrida de e-mail duplicado em erro de validação', function () {
    // simula o segundo de dois envios simultâneos: a validação passou, mas o e-mail já foi gravado
    User::factory()->create(['email' => 'ana.silva@academico.ifsul.edu.br']);

    expect(fn () => app(RegisterUser::class)->handle('Ana Silva', 'ana.silva@academico.ifsul.edu.br', 'segredo123', config('conectacc.terms_version')))
        ->toThrow(fn (ValidationException $e) => expect($e->errors())->toBe(['email' => ['Não foi possível criar uma conta com este e-mail. Se você já possui cadastro, entre.']]));
});

it('limita o Cadastro a 5 tentativas por minuto por IP', function () {
    foreach (range(1, 5) as $i) {
        $this->postJson('/api/v1/register', registerPayload(['email' => "ana{$i}@gmail.com"]))->assertUnprocessable();
    }

    $this->postJson('/api/v1/register', registerPayload())
        ->assertTooManyRequests()
        ->assertJsonPath('message', fn (string $message) => str_starts_with($message, 'Muitas tentativas. Tente novamente em '));
});
