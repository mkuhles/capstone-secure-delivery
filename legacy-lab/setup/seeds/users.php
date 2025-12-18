<?php
declare(strict_types=1);

/**
 * Seeds users without making DB inconsistent:
 * - creates missing users
 * - updates is_admin to desired value
 * - does NOT overwrite passwords unless forced
 */
return function (PDO $pdo, array $seedUsers, bool $forcePasswords): void {
    $find = $pdo->prepare("SELECT id, password_hash, is_admin FROM users WHERE username = :u");
    $ins  = $pdo->prepare("INSERT INTO users(username, password_hash, is_admin) VALUES(:u, :ph, :a)");
    $updA = $pdo->prepare("UPDATE users SET is_admin = :a WHERE id = :id");
    $updP = $pdo->prepare("UPDATE users SET password_hash = :ph WHERE id = :id");

    foreach ($seedUsers as [$u, $plain, $isAdmin]) {
        $find->execute([':u' => $u]);
        $row = $find->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $ins->execute([
                ':u'  => $u,
                ':ph' => password_hash($plain, PASSWORD_DEFAULT),
                ':a'  => $isAdmin,
            ]);
            continue;
        }

        if ($forcePasswords) {
            $updP->execute([
                ':ph' => password_hash($plain, PASSWORD_DEFAULT),
                ':id' => (int)$row['id'],
            ]);
        }

        if ((int)$row['is_admin'] !== (int)$isAdmin) {
            $updA->execute([
                ':a'  => $isAdmin,
                ':id' => (int)$row['id'],
            ]);
        }
    }
};
