<?php

use App\Actions\ChangePassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

const OLD_PASSWORD = 'senha-antiga';

function passwordOwner(): User
{
    return User::factory()->create(['password' => OLD_PASSWORD]);
}

function changePassword($test, User $user, array $overrides = [])
{
    return $test->actingAs($user, 'web')->putJson('/api/v1/me/password', [...[
        'current_password' => OLD_PASSWORD,
        'password' => 'senha-nova-1',
        'password_confirmation' => 'senha-nova-1',
    ], ...$overrides]);
}

function addSession(string $id, User $user): void
{
    DB::table('sessions')->insert(['id' => $id, 'user_id' => $user->id, 'payload' => '', 'last_activity' => now()->timestamp]);
}

it('troca a senha', function () {
    $user = passwordOwner();

    changePassword($this, $user)->assertNoContent();

    $hash = $user->fresh()->password;
    expect(Hash::check('senha-nova-1', $hash))->toBeTrue()
        ->and(Hash::check(OLD_PASSWORD, $hash))->toBeFalse();
});

it('aceita nova senha igual à atual', function () {
    changePassword($this, passwordOwner(), ['password' => OLD_PASSWORD, 'password_confirmation' => OLD_PASSWORD])
        ->assertNoContent();
});

it('preserva espaços nas pontas da senha', function () {
    $user = passwordOwner();

    changePassword($this, $user, ['password' => '  segredo novo  ', 'password_confirmation' => '  segredo novo  '])
        ->assertNoContent();

    $hash = $user->fresh()->password;
    expect(Hash::check('  segredo novo  ', $hash))->toBeTrue()
        ->and(Hash::check('segredo novo', $hash))->toBeFalse();
});

it('valida cada campo e mantém a senha', function (array $overrides, string $field, string $message) {
    $user = passwordOwner();

    changePassword($this, $user, $overrides)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field => $message]);

    expect(Hash::check(OLD_PASSWORD, $user->fresh()->password))->toBeTrue();
})->with([
    'senha atual vazia' => [['current_password' => ''], 'current_password', 'Informe sua senha atual.'],
    'senha atual incorreta' => [['current_password' => 'outra-senha'], 'current_password', 'A senha atual informada está incorreta.'],
    'nova senha vazia' => [['password' => ''], 'password', 'Informe sua nova senha.'],
    'nova senha curta' => [['password' => '1234567', 'password_confirmation' => '1234567'], 'password', 'Use pelo menos 8 caracteres.'],
    'confirmação vazia' => [['password_confirmation' => ''], 'password_confirmation', 'Confirme sua nova senha.'],
    'confirmação diferente' => [['password_confirmation' => 'outra-senha-9'], 'password_confirmation', 'As senhas informadas não coincidem.'],
]);

it('mostra todos os erros juntos', function () {
    changePassword($this, passwordOwner(), ['current_password' => 'outra-senha', 'password' => 'abc', 'password_confirmation' => 'abc'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['current_password', 'password']);
});

it('encerra só as outras sessões', function () {
    $user = passwordOwner();
    addSession('atual', $user);
    addSession('outra', $user);
    addSession('alheia', User::factory()->create());

    app(ChangePassword::class)->handle($user, 'senha-nova-1', 'atual');

    expect(DB::table('sessions')->orderBy('id')->pluck('id')->all())->toBe(['alheia', 'atual']);
});

it('encerra as outras sessões pela rota', function () {
    $user = passwordOwner();
    addSession('outra', $user);

    changePassword($this, $user)->assertNoContent();

    expect(DB::table('sessions')->where('id', 'outra')->exists())->toBeFalse();
});

it('não encerra sessões quando a troca falha', function () {
    $user = passwordOwner();
    addSession('outra', $user);

    changePassword($this, $user, ['current_password' => 'outra-senha'])->assertUnprocessable();

    expect(DB::table('sessions')->where('id', 'outra')->exists())->toBeTrue();
});

it('limita a 5 tentativas por usuário', function () {
    $user = passwordOwner();

    foreach (range(1, 5) as $ignored) {
        changePassword($this, $user, ['current_password' => 'outra-senha'])->assertUnprocessable();
    }

    $response = changePassword($this, $user, ['current_password' => 'outra-senha'])->assertTooManyRequests();
    expect($response->json('message'))->toStartWith('Muitas tentativas. Tente novamente em');

    // entre requisições do teste, a sessão guarda o hash da senha e o guard do Sanctum guarda o usuário anterior
    $this->flushSession();
    app('auth')->forgetGuards();
    changePassword($this, passwordOwner())->assertNoContent();
});

it('com sessão real, o cookie antigo deixa de valer e o novo continua', function () {
    // o phpunit.xml usa o driver array; a regra só existe de verdade com as linhas na tabela sessions
    config(['session.driver' => 'database']);
    app('session')->forgetDrivers();
    $user = passwordOwner();
    $name = config('session.cookie');
    $withSession = function (string $id) use ($name) {
        app('auth')->forgetGuards();
        app()->forgetInstance('session.store');
        app('session')->forgetDrivers();

        return $this->withCredentials()->withCookie($name, $id);
    };

    $old = $this->withCredentials()->postJson('/api/v1/login', ['email' => $user->email, 'password' => OLD_PASSWORD])
        ->assertOk()->getCookie($name)->getValue();
    $withSession($old)->getJson('/api/v1/me')->assertOk();

    $new = $withSession($old)->putJson('/api/v1/me/password', [
        'current_password' => OLD_PASSWORD,
        'password' => 'senha-nova-1',
        'password_confirmation' => 'senha-nova-1',
    ])->assertNoContent()->getCookie($name)->getValue();

    expect($new)->not->toBe($old)
        ->and(DB::table('sessions')->where('id', $old)->exists())->toBeFalse();
    $withSession($old)->getJson('/api/v1/me')->assertUnauthorized();
    $withSession($new)->getJson('/api/v1/me')->assertOk();
});

it('exige sessão', function () {
    $this->putJson('/api/v1/me/password', [])->assertUnauthorized();
});

it('expõe o e-mail só para a própria pessoa', function () {
    $user = User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    $this->actingAs($user, 'web')->getJson('/api/v1/me')->assertJsonPath('email', 'ana@academico.ifsul.edu.br');

    $this->actingAs(User::factory()->create(), 'web')->getJson("/api/v1/students/{$user->fresh()->public_code}")
        ->assertOk()
        ->assertJsonMissingPath('email');
});
