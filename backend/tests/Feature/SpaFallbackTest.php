<?php

it('serve a SPA nas rotas do front e não intercepta a API', function () {
    fakeSpaBuild();

    $this->get('/home')->assertOk()->assertSee('id="root"', false);
    $this->get('/')->assertOk()->assertSee('id="root"', false);
    $this->getJson('/api/v1/nao-existe')->assertNotFound()->assertHeader('Content-Type', 'application/json');
});
