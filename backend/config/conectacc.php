<?php

return [
    'allowed_email_domains' => array_values(array_filter(array_map(
        fn (string $domain): string => strtolower(trim($domain)),
        explode(',', (string) env('ALLOWED_EMAIL_DOMAINS', 'academico.ifsul.edu.br')),
    ))),

    'terms_version' => (string) env('TERMS_VERSION', '0.2'),
];
