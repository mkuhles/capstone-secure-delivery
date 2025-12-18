<?php
declare(strict_types=1);

return [
    'version' => 2,
    'up' => function (PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_notes (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              note TEXT NOT NULL,
              created_at TEXT NOT NULL,
              created_by_user_id INTEGER NOT NULL,
              FOREIGN KEY(created_by_user_id) REFERENCES users(id)
            );
        ");
    },
];
