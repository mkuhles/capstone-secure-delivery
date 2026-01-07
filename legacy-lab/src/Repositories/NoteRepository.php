<?php
declare(strict_types=1);

namespace LegacyLab\Repositories;

use PDO;
use LegacyLab\Entities\Note;

final class NoteRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return Note[] */
    public function listByOwner(int $ownerUserId, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $stmt = $this->pdo->prepare(
            "SELECT id, owner_user_id, content, created_at
             FROM notes
             WHERE owner_user_id = :owner
             ORDER BY id DESC
             LIMIT $limit"
        );
        $stmt->execute(['owner' => $ownerUserId]);

        $notes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notes[] = new Note(
                id: (int)$row['id'],
                ownerUserId: (int)$row['owner_user_id'],
                content: (string)$row['content'],
                createdAt: (string)$row['created_at']
            );
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $notes;
    }

    public function findById(int $id): ?Note
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, owner_user_id, content, created_at
             FROM notes
             WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        return new Note(
            id: (int)$row['id'],
            ownerUserId: (int)$row['owner_user_id'],
            content: (string)$row['content'],
            createdAt: (string)$row['created_at']
        );
    }

    public function create(int $ownerUserId, string $content): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notes (owner_user_id, content, created_at)
             VALUES (:owner, :content, :created_at)"
        );
        $stmt->execute([
            'owner' => $ownerUserId,
            'content' => $content,
            'created_at' => gmdate('c'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateContent(int $id, string $content): void {
        $stmt = $this->pdo->prepare("UPDATE notes SET content = :c WHERE id = :id");
        $stmt->execute(['c' => $content, 'id' => $id]);
    }
}
