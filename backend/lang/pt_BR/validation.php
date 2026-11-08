<?php

// regras padrão do painel e de entradas que a SPA nunca envia (lista no lugar de texto); a API define as próprias mensagens nos Form Requests
return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um e-mail válido.',
    'in' => 'A opção escolhida em :attribute é inválida.',
    'string' => 'O valor enviado precisa ser um texto.',
    'boolean' => 'O valor enviado precisa ser verdadeiro ou falso.',
];
