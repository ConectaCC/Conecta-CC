<?php

it('devolve os Termos vigentes com versão, seções e frase de aceite', function () {
    $this->getJson('/api/v1/terms/current')
        ->assertOk()
        ->assertJsonPath('version', '0.2')
        ->assertJsonPath('title', 'Termos de participação na versão de testes')
        ->assertJsonCount(4, 'sections')
        ->assertJsonStructure(['version', 'title', 'sections' => [['title', 'body']], 'acceptance_label'])
        ->assertJsonPath('sections.3.body', fn (string $body) => str_contains($body, 'Se você escolher compartilhar anonimamente'));
});
