<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Ana Silva',
        'display_name' => 'Ana Silva',
        'email' => 'aluno@academico.ifsul.edu.br',
        'password' => 'segredo123',
    ]);
});

it('entra com credenciais corretas e devolve o usuário', function () {
    $this->postJson('/api/v1/login', ['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'segredo123'])
        ->assertOk()
        ->assertExactJson([
            'id' => $this->user->id,
            'name' => 'Ana Silva',
            'email' => 'aluno@academico.ifsul.edu.br',
            'display_name' => 'Ana Silva',
            'initials' => 'AS',
            'role' => 'ALUNO',
            'profile_complete' => false,
        ]);

    $this->assertAuthenticatedAs($this->user, 'web');
});

it('aceita o e-mail com maiúsculas e espaços', function () {
    $this->postJson('/api/v1/login', ['email' => '  Aluno@Academico.IFSUL.edu.br ', 'password' => 'segredo123'])
        ->assertOk();
});

it('usa a mesma mensagem para e-mail inexistente e senha errada', function (string $email, string $password) {
    $this->postJson('/api/v1/login', ['email' => $email, 'password' => $password])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['credentials' => 'E-mail ou senha incorretos.']);

    $this->assertGuest('web');
})->with([
    'e-mail inexistente' => ['ninguem@academico.ifsul.edu.br', 'segredo123'],
    'senha errada' => ['aluno@academico.ifsul.edu.br', 'senha-errada'],
]);

it('valida os campos', function (array $payload, string $field, string $message) {
    $this->postJson('/api/v1/login', [...['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'segredo123'], ...$payload])
        ->assertUnprocessable()
        ->assertJsonPath("errors.{$field}", [$message]);

    $this->assertGuest('web');
})->with([
    'e-mail vazio' => [['email' => ''], 'email', 'Informe um endereço de e-mail válido.'],
    'e-mail incompleto' => [['email' => 'aluno@'], 'email', 'Informe um endereço de e-mail válido.'],
    'senha vazia' => [['password' => ''], 'password', 'Informe sua senha.'],
    'senha como lista' => [['password' => ['segredo123']], 'password', 'O valor enviado precisa ser um texto.'],
]);

it('exige e-mail e senha mesmo quando não vêm no corpo', function () {
    $this->postJson('/api/v1/login', [])
        ->assertUnprocessable()
        ->assertJsonPath('errors.email', ['Informe um endereço de e-mail válido.'])
        ->assertJsonPath('errors.password', ['Informe sua senha.']);
});

it('bloqueia a 6ª tentativa no mesmo minuto', function () {
    foreach (range(1, 5) as $ignored) {
        $this->postJson('/api/v1/login', ['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'senha-errada'])
            ->assertUnprocessable();
    }

    $this->postJson('/api/v1/login', ['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'segredo123'])
        ->assertTooManyRequests()
        ->assertJsonPath('message', fn (string $message) => preg_match('/^Muitas tentativas\. Tente novamente em [1-9]\d* segundos\.$/', $message) === 1);
});

it('conta as tentativas pelo e-mail, sem diferenciar maiúsculas, e não bloqueia outro e-mail', function () {
    foreach (['aluno@academico.ifsul.edu.br', 'ALUNO@academico.ifsul.edu.br'] as $email) {
        foreach (range(1, 3) as $ignored) {
            $this->postJson('/api/v1/login', ['email' => $email, 'password' => 'senha-errada']);
        }
    }

    $this->postJson('/api/v1/login', ['email' => 'Aluno@Academico.ifsul.edu.br', 'password' => 'segredo123'])->assertTooManyRequests();
    $this->postJson('/api/v1/login', ['email' => 'outro@academico.ifsul.edu.br', 'password' => 'x'])->assertUnprocessable();
});
