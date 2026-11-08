<?php

use App\Models\User;

it('devolve 401 em /me sem sessão', function () {
    $this->getJson('/api/v1/me')->assertUnauthorized();
});

it('devolve o usuário logado em /me', function () {
    $user = User::factory()->moderator()->create(['display_name' => 'Marcelo Henrique Germiniani Panho']);

    $this->actingAs($user, 'web')
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJson(['id' => $user->id, 'role' => 'MODERADOR', 'initials' => 'MP']);
});

it('encerra a sessão no logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')->withSession(['guardado' => 'x']);
    $before = ['id' => session()->getId(), 'token' => session()->token()];

    $this->postJson('/api/v1/logout')->assertNoContent();

    $this->assertGuest('web');
    // a sessão antiga e o token CSRF antigo deixam de valer
    expect(session()->has('guardado'))->toBeFalse()
        ->and(session()->getId())->not->toBe($before['id'])
        ->and(session()->token())->not->toBe($before['token']);
});

it('troca o id da sessão e o token CSRF no login', function () {
    User::factory()->create(['email' => 'ana@academico.ifsul.edu.br', 'password' => 'segredo123']);
    $this->withSession(['_token' => 'token-antigo']);
    $before = session()->getId();

    $this->postJson('/api/v1/login', ['email' => 'ana@academico.ifsul.edu.br', 'password' => 'segredo123'])->assertOk();

    expect(session()->getId())->not->toBe($before)
        ->and(session()->token())->not->toBe('token-antigo');
});
