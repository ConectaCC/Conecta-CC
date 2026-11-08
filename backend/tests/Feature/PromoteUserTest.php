<?php

use App\Enums\Role;
use App\Models\User;

it('promove um usuário existente sem imprimir o e-mail', function () {
    $user = User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    $this->artisan('conectacc:promote', ['email' => ' ANA@academico.ifsul.edu.br', 'role' => ' administrador '])
        ->expectsOutput('Papel atualizado para ADMINISTRADOR.')
        ->doesntExpectOutputToContain('ana@academico.ifsul.edu.br')
        ->assertSuccessful();

    expect($user->fresh()->role)->toBe(Role::Administrador);
});

it('recusa papel inválido', function () {
    User::factory()->create(['email' => 'ana@academico.ifsul.edu.br']);

    $this->artisan('conectacc:promote', ['email' => 'ana@academico.ifsul.edu.br', 'role' => 'SUPERADMIN'])
        ->expectsOutput('Papel inválido. Use ALUNO, MODERADOR ou ADMINISTRADOR.')
        ->assertFailed();
});

it('recusa e-mail inexistente sem repetir o e-mail', function () {
    $this->artisan('conectacc:promote', ['email' => 'ninguem@academico.ifsul.edu.br', 'role' => 'MODERADOR'])
        ->expectsOutput('Nenhum usuário encontrado com este e-mail.')
        ->doesntExpectOutputToContain('ninguem@')
        ->assertFailed();
});
