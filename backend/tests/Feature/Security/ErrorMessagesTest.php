<?php

use App\Models\User;

// erros da API em português e sem nomes de classes internas (decisoes.md §78)

it('responde 404 em português, sem revelar o modelo procurado', function (string $url) {
    $this->actingAs(User::factory()->create())->getJson($url)
        ->assertNotFound()
        ->assertExactJson(['message' => 'Não encontramos o que você procurou.']);
})->with([
    'estudante inexistente' => '/api/v1/students/0123456789abcdef',
    'rota inexistente' => '/api/v1/nao-existe',
]);

it('responde 405 em português', function () {
    $this->deleteJson('/api/v1/terms/current')
        ->assertStatus(405)
        ->assertExactJson(['message' => 'Esta ação não é permitida neste endereço.']);
});
