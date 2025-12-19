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
];
