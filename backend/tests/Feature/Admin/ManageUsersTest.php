<?php

use App\Actions\ChangeUserRole;
use App\Enums\Role;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->create(['role' => Role::Administrador]);
    $this->actingAs($this->admin, 'web');
});

it('lista e busca por nome, nome de exibição e e-mail', function (string $search) {
    $found = User::factory()->create(['name' => 'Beatriz Souza', 'display_name' => 'Bia', 'email' => 'beatriz@academico.ifsul.edu.br']);
    $other = User::factory()->create(['name' => 'Carlos Lima', 'display_name' => 'Cadu', 'email' => 'carlos@academico.ifsul.edu.br']);

    Livewire::test(ListUsers::class)
        ->searchTable($search)
        ->assertCanSeeTableRecords([$found])
        ->assertCanNotSeeTableRecords([$other]);
})->with(['Beatriz', 'BIA', 'beatriz@']);

it('filtra por papel', function () {
    $moderator = User::factory()->create(['role' => Role::Moderador]);
    $student = User::factory()->create(['role' => Role::Aluno]);

    Livewire::test(ListUsers::class)
        ->filterTable('role', Role::Moderador->value)
        ->assertCanSeeTableRecords([$moderator])
        ->assertCanNotSeeTableRecords([$student]);
});

it('altera para cada papel sem mudar outros campos', function (Role $from, Role $to) {
    $user = User::factory()->create(['role' => $from, 'name' => 'Beatriz Souza']);
    $before = $user->fresh()->only(['name', 'display_name', 'email', 'password']);

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('changeRole')->table($user), ['role' => $to->value])
        ->assertHasNoErrors()
        ->assertNotified('Papel atualizado.');

    expect($user->fresh()->role)->toBe($to)
        ->and($user->fresh()->only(['name', 'display_name', 'email', 'password']))->toBe($before);
})->with([
    [Role::Aluno, Role::Moderador],
    [Role::Aluno, Role::Administrador],
    [Role::Moderador, Role::Aluno],
    [Role::Administrador, Role::Aluno],
]);

it('recusa um papel que não existe', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('changeRole')->table($user), ['role' => 'SUPERADMIN'])
        ->assertHasActionErrors(['role']);

    expect($user->fresh()->role)->toBe(Role::Aluno);
});

it('avisa no modal o que o papel de ADMINISTRADOR libera', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->assertActionExists(TestAction::make('changeRole')->table($user), fn (Action $action): bool => $action->getModalDescription() === 'Quem for definido como ADMINISTRADOR passa a acessar este painel e a identidade dos autores de materiais anônimos.'
            && $action->getModalSubmitActionLabel() === 'Salvar papel');
});

it('esconde a ação na própria linha e ignora a chamada forjada', function () {
    Livewire::test(ListUsers::class)
        ->assertActionHidden(TestAction::make('changeRole')->table($this->admin))
        ->call('mountAction', 'changeRole', [], ['table' => true, 'recordKey' => (string) $this->admin->id])
        ->set('mountedActions.0.data.role', Role::Aluno->value)
        ->call('callMountedAction');

    expect($this->admin->fresh()->role)->toBe(Role::Administrador);
});

it('recusa na regra a mudança do próprio papel', function () {
    expect(fn () => app(ChangeUserRole::class)->handle($this->admin, $this->admin, Role::Aluno))
        ->toThrow(fn (HttpException $e) => expect($e->getStatusCode())->toBe(403));

    expect($this->admin->fresh()->role)->toBe(Role::Administrador);
});

it('recusa na regra quem não é ADMINISTRADOR', function (Role $role) {
    $actor = User::factory()->create(['role' => $role]);
    $target = User::factory()->create();

    expect(fn () => app(ChangeUserRole::class)->handle($actor, $target, Role::Administrador))
        ->toThrow(fn (HttpException $e) => expect($e->getStatusCode())->toBe(403));

    expect($target->fresh()->role)->toBe(Role::Aluno);
})->with([Role::Aluno, Role::Moderador]);

it('registra a mudança só com IDs internos', function () {
    $target = User::factory()->create(['name' => 'Beatriz Souza', 'email' => 'beatriz@academico.ifsul.edu.br']);
    Log::spy();

    app(ChangeUserRole::class)->handle($this->admin, $target, Role::Moderador);

    Log::shouldHaveReceived('info')->once()->with('Papel alterado', [
        'actor_id' => $this->admin->id,
        'target_id' => $target->id,
        'from' => 'ALUNO',
        'to' => 'MODERADOR',
    ]);
});

it('não grava nem registra quando o papel é o mesmo', function () {
    $target = User::factory()->create(['role' => Role::Moderador]);
    $updatedAt = $target->fresh()->updated_at;
    $this->travel(1)->minute();
    Log::spy();

    app(ChangeUserRole::class)->handle($this->admin, $target, Role::Moderador);

    Log::shouldNotHaveReceived('info');
    expect($target->fresh()->updated_at->equalTo($updatedAt))->toBeTrue();
});

it('não oferece criar, editar nem excluir', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->assertActionDoesNotExist('create')
        ->assertActionDoesNotExist(TestAction::make('edit')->table($user))
        ->assertActionDoesNotExist(TestAction::make('delete')->table($user));
});
