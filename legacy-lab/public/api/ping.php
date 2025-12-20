<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz, CSRF protected

require __DIR__ . '/../../vendor/autoload.php';
use LegacyLab\Core\Bootstrap;

$container = Bootstrap::container();
$cors = $container->cors();
$cors->handle();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    // respond with pong
    echo json_encode(['ok' => true, 'message' => 'pong']);
    exit;
} else {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}