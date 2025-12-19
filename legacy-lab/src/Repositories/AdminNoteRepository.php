<?php
declare(strict_types=1);
namespace LegacyLab\Repositories;

use PDO;
use DateTime;
use LegacyLab\Entities\AdminNote;

final class AdminNoteRepository {
  public function __construct(private PDO $pdo) {}

  public function create(string $note, int $createdByUserId): AdminNote {
    $stmt = $this->pdo->prepare('INSERT INTO admin_notes (note, created_at, created_by_user_id) VALUES (:note, :created_at, :created_by_user_id)');
    $createdAt = (new DateTime())->format('Y-m-d H:i:s');
    $stmt->execute([
      'note' => $note,
      'created_at' => $createdAt,
      'created_by_user_id' => $createdByUserId
    ]);
    $id = (int)$this->pdo->lastInsertId();
    return new AdminNote(
      id: $id,
      note: $note,
      createdAt: $createdAt,
      createdByUserId: $createdByUserId
    );
  }

  public function findAll(): array {
    $stmt = $this->pdo->query('SELECT id, note, created_at, created_by_user_id FROM admin_notes ORDER BY created_at DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $notes = [];
    foreach ($rows as $row) {
      $notes[] = new AdminNote(
        id: (int)$row['id'],
        note: (string)$row['note'],
        createdAt: (string)$row['created_at'],
        createdByUserId: (int)$row['created_by_user_id']
      );
    }
    return $notes;
  }

  public function latestNotes(int $limit): array {
    $stmt = $this->pdo->prepare('
      SELECT an.id, an.note, an.created_at, an.created_by_user_id
      FROM admin_notes an
      ORDER BY an.id DESC
      LIMIT :limit
    ');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
      $notes[] = new AdminNote(
        id: (int)$row['id'],
        note: (string)$row['note'],
        createdAt: (string)$row['created_at'],
        createdByUserId: (int)$row['created_by_user_id'],
      );
    }
    return $notes;
  }
}