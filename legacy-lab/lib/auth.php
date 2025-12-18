<?php
declare(strict_types=1);

function current_user(PDO $pdo): ?array
{
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) return null;

    $stmt = $pdo->prepare('SELECT id, username, is_admin FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => (int)$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function require_login(PDO $pdo): array
{
    $user = current_user($pdo);
    if (!$user) {
        header('Location: /login.php');
        exit;
    }
    return $user;
}

function require_admin(PDO $pdo): array
{
    $user = require_login($pdo);
    if (!(bool)((int)$user['is_admin'])) {
        http_response_code(403);
        echo "Forbidden <a href=\"/index.php\">Go back</a>";
        exit;
    }
    return $user;
}
