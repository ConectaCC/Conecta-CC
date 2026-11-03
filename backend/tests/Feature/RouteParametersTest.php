<?php

use App\Models\User;

it('responde 404, e não 500, para id em formato inválido na URL', function (string $method, string $url) {
    $this->actingAs(User::factory()->administrator()->create())->json($method, $url)->assertNotFound();
})->with([
    'notificação que não é UUID' => ['POST', '/api/v1/notifications/abc/read'],
    'material não numérico (cancelar)' => ['POST', '/api/v1/materials/abc/cancel'],
    'material não numérico (editar)' => ['PUT', '/api/v1/materials/abc'],
    'material não numérico (excluir)' => ['DELETE', '/api/v1/materials/abc'],
    'material não numérico (aprovar)' => ['POST', '/api/v1/moderation/materials/abc/approve'],
    'material não numérico (rejeitar)' => ['POST', '/api/v1/moderation/materials/abc/reject'],
    'disciplina não numérica (lista)' => ['GET', '/api/v1/disciplines/abc/materials'],
    'disciplina não numérica (envio)' => ['POST', '/api/v1/disciplines/abc/materials'],
]);
