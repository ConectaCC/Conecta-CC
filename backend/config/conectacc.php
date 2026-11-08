<?php

return [
    'allowed_email_domains' => array_values(array_filter(array_map(
        fn (string $domain): string => strtolower(trim($domain)),
        explode(',', (string) env('ALLOWED_EMAIL_DOMAINS', 'academico.ifsul.edu.br')),
    ))),

    // período de teste fechado (decisoes.md §79): com a lista preenchida, só esses e-mails criam conta; vazia, o cadastro fica aberto ao domínio
    'allowed_emails' => array_values(array_filter(array_map(
        fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('ALLOWED_EMAILS', '')),
    ))),

    'terms_version' => (string) env('TERMS_VERSION', '0.2'),
];
