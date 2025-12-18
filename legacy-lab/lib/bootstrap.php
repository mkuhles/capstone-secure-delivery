<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$config = require __DIR__ . '/../setup/config.php';

// Session management
require_once __DIR__ . '/Session.php';
$session = new Session(); // optional: Session('LEGACYSESSID')
$session->start();

$pdo = db_connect($config['db_file']);

// Authentication and Authorization
require_once __DIR__ . '/Auth.php';
$auth = new Auth($pdo, $session);

// CSRF protection
require_once __DIR__ . '/Csrf.php';
$csrf = new Csrf($session, (bool)($config['csrf_enabled'] ?? true));