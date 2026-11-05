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

it('bloqueia a 6ª tentativa no mesmo minuto', function () {
    foreach (range(1, 5) as $ignored) {
        $this->postJson('/api/v1/login', ['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'senha-errada'])
            ->assertUnprocessable();
    }

    $this->postJson('/api/v1/login', ['email' => 'aluno@academico.ifsul.edu.br', 'password' => 'segredo123'])
        ->assertTooManyRequests()
        ->assertJsonPath('message', fn (string $message) => str_starts_with($message, 'Muitas tentativas. Tente novamente em '));
});
