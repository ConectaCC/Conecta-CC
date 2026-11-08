<?php

// a lista vem de uma variável de ambiente digitada à mão no Render

it('lê a lista de convidados sem espaços, sem maiúsculas e sem itens vazios', function () {
    putenv('ALLOWED_EMAILS= Ana@academico.ifsul.edu.br , ,bia@academico.ifsul.edu.br,');

    try {
        $config = require dirname(__DIR__, 2).'/config/conectacc.php';
    } finally {
        putenv('ALLOWED_EMAILS');
    }

    expect($config['allowed_emails'])->toBe(['ana@academico.ifsul.edu.br', 'bia@academico.ifsul.edu.br']);
});

it('sem a variável, a lista fica vazia e o cadastro aberto', function () {
    putenv('ALLOWED_EMAILS');

    $config = require dirname(__DIR__, 2).'/config/conectacc.php';

    expect($config['allowed_emails'])->toBe([]);
});
