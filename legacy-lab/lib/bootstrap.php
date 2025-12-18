<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$config = require __DIR__ . '/../setup/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pdo = db_connect($config['db_file']);

// CSRF protection
require_once __DIR__ . '/Csrf.php';
$csrf = new Csrf((bool)($config['csrf_enabled'] ?? true));