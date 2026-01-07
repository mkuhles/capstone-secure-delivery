<?php
declare(strict_types=1);

return [
    'version' => 3,
    'up' => function (PDO $pdo): void {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS notes (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              owner_user_id INTEGER NOT NULL,
              content TEXT NOT NULL,
              created_at TEXT NOT NULL,
              FOREIGN KEY(owner_user_id) REFERENCES users(id)
            );
        ");
        $pdo->exec("
        INSERT INTO notes (owner_user_id, content, created_at) VALUES
            (1, 'Note from user 1', '2026-01-07T10:00:00Z'),
            (2, 'Note from user 2', '2026-01-07T10:00:00Z');
        ");
    },
];