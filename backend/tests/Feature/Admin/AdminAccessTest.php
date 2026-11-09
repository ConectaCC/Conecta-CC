<?php

use App\Enums\Role;
use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;

function panelUser(Role $role = Role::Administrador, array $attributes = []): User
{
    return User::factory()->create(['role' => $role, ...$attributes]);
}

it('leva quem não tem sessão para o login do painel', function () {
    $this->get('/admin/usuarios')->assertRedirect('/admin/login');
});

it('recusa ALUNO e MODERADOR com a página em português', function (Role $role) {
    $this->actingAs(panelUser($role), 'web')->get('/admin/usuarios')
        ->assertForbidden()
        ->assertSee('Você não tem permissão para acessar esta página.')
        ->assertSee('Voltar ao ConectaCC');
})->with([Role::Aluno, Role::Moderador]);

it('abre o painel para o ADMINISTRADOR', function () {
    $this->actingAs(panelUser(attributes: ['display_name' => 'Ana Admin']), 'web')->get('/admin')->assertRedirect('/admin/usuarios');
    $this->get('/admin/usuarios')->assertSee('<span class="conectacc-user-name">Ana Admin</span>', false);
    $this->get('/admin/usuarios')->assertOk()->assertSee('Usuários')->assertSee('Voltar ao ConectaCC');
});

it('mostra o nome e as iniciais sem enviar o nome a serviços externos', function () {
    $this->actingAs(panelUser(attributes: ['display_name' => 'Alice Administradora']), 'web')->get('/admin/usuarios')
        ->assertOk()
        ->assertSee('Alice Administradora')
        ->assertSee('css/conectacc-admin.css')
        ->assertDontSee('ui-avatars.com');
});

it('tira o acesso na requisição seguinte quando o papel muda', function () {
    $admin = panelUser();
    $this->actingAs($admin, 'web')->get('/admin/usuarios')->assertOk();

    $admin->role = Role::Aluno;
    $admin->save();

    $this->get('/admin/usuarios')->assertForbidden();
});

it('aceita no login do painel o e-mail como o app aceita', function () {
    Filament::setCurrentPanel('admin');
    $admin = panelUser(attributes: ['email' => 'admin@academico.ifsul.edu.br', 'password' => 'senha-segura']);

    Livewire::test(Login::class)
        ->set('data.email', '  ADMIN@academico.ifsul.edu.br ')
        ->set('data.password', 'senha-segura')
        ->call('authenticate')
        ->assertHasNoErrors();

    $this->assertAuthenticatedAs($admin, 'web');
});

it('recusa no login do painel quem não é ADMINISTRADOR', function () {
    Filament::setCurrentPanel('admin');
    panelUser(Role::Moderador, ['email' => 'mod@academico.ifsul.edu.br', 'password' => 'senha-segura']);

    Livewire::test(Login::class)
        ->set('data.email', 'mod@academico.ifsul.edu.br')
        ->set('data.password', 'senha-segura')
        ->call('authenticate')
        ->assertHasErrors(['data.email']);

    $this->assertGuest('web');
});

it('mostra em português a validação do login e não oferece "lembrar de mim"', function () {
    Filament::setCurrentPanel('admin');

    Livewire::test(Login::class)
        ->assertDontSee('data.remember')
        ->assertSee('Painel administrativo')
        ->assertSee('Entrar')
        ->call('authenticate')
        ->assertHasErrors(['data.email'])
        ->assertSee('obrigatório')
        ->assertDontSee('validation.required');
});

it('compartilha a sessão com o app, inclusive depois da troca de senha', function () {
    // o phpunit.xml usa o driver array; aqui a sessão precisa existir de verdade entre as requisições
    config(['session.driver' => 'database']);
    app('session')->forgetDrivers();
    $admin = panelUser(attributes: ['password' => 'senha-antiga']);
    $name = config('session.cookie');
    $withSession = function (string $id) use ($name) {
        app('auth')->forgetGuards();
        app()->forgetInstance('session.store');
        app('session')->forgetDrivers();

        return $this->withCredentials()->withCookie($name, $id);
    };

    $session = $this->withCredentials()->postJson('/api/v1/login', ['email' => $admin->email, 'password' => 'senha-antiga'])
        ->assertOk()->getCookie($name)->getValue();
    $withSession($session)->get('/admin/usuarios')->assertOk();

    $renewed = $withSession($session)->putJson('/api/v1/me/password', [
        'current_password' => 'senha-antiga',
        'password' => 'senha-nova-1',
        'password_confirmation' => 'senha-nova-1',
    ])->assertNoContent()->getCookie($name)->getValue();

    $withSession($renewed)->get('/admin/usuarios')->assertOk();
    $withSession($session)->get('/admin/usuarios')->assertRedirect('/admin/login');
});

it('não deixa a SPA capturar as rotas do painel e do Livewire', function () {
    fakeSpaBuild();

    $this->get('/home')->assertOk()->assertSee('id="root"', false);
    $this->get('/admin/login')->assertOk()->assertDontSee('id="root"', false);
    $this->get(EndpointResolver::prefix().'/livewire.js')->assertOk()->assertDontSee('id="root"', false);
});
