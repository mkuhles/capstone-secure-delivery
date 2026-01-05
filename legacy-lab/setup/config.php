<?php
declare(strict_types=1);

return [
    'db_file' => __DIR__ . '/../var/lab.sqlite',

    // Seeds zentral – hier änderst du Test-User/Passwörter/Rollen.
    'seed_users' => [
        // username, plainPassword, is_admin
        ['user',  'user',  0],
        ['admin', 'admin', 1],
    ],

    // vulnerability switches
    'csrf_protected' => true,
    'xss_protected' => true,
    'sqli_protected' => true,
    'cors_protected' => true,
    'log_injection_protected' => false,


    // logging configuration
    'logging_structured' => true,
    'logging_file' => __DIR__ . '/../var/log/legacy.jsonl',
    'canonical_log_lines' => false,
    'logging_file_canonical' => __DIR__ . '/../var/log/legacy_canonical.jsonl',
    'trusted_proxies' => ['127.0.0.1'], // später: IP/CIDR vom Ingress/Proxy

    // security headers configuration
    'security_headers' => [
        'x_frame_options' => true,
    ],


    // allowlist of trusted browser origins (scheme + host + port)
    'cors_allowed_origins' => [
        'http://127.0.0.1:8081',
    ],

    // only relevant for credentialed cross-origin requests (cookies/auth)
    // (we'll set this to true in the vulnerable mode later to demonstrate the risk)
    'cors_allow_credentials' => false,
];
