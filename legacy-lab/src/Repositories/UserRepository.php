<?php
declare(strict_types=1);
namespace LegacyLab\Repositories;

use PDO;
use LegacyLab\Entities\User;

final class UserRepository {
  public function __construct(private PDO $pdo) {}

  public function findById(int $id): ?User {
    $stmt = $this->pdo->prepare('SELECT id, username, is_admin FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return null;
    }
    return new User(
      id: (int)$row['id'],
      username: (string)$row['username'],
      isAdmin: (bool)((int)$row['is_admin'])
    );
  }

  public function findByUsername(string $username): ?User {
    $stmt = $this->pdo->prepare('SELECT id, username, is_admin FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return null;
    }
    return new User(
      id: (int)$row['id'],
      username: (string)$row['username'],
      isAdmin: (bool)((int)$row['is_admin'])
    );
  }

  public function verifyPassword(string $username, string $password): ?User {
    $stmt = $this->pdo->prepare('SELECT id, username, is_admin, password_hash FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return null;
    }
    if (!password_verify($password, (string)$row['password_hash'])) {
      return null;
    }
    return new User(
      id: (int)$row['id'],
      username: (string)$row['username'],
      isAdmin: (bool)((int)$row['is_admin'])
    );
  }

   /**
   * SQLi demo: when $protected is false, this uses string concatenation (DO NOT DO THIS IN REAL APPS).
   * When $protected is true, it uses a prepared statement.
   *
   * @return User[]
   */
  public function searchByUsername(string $q, bool $protected = true, int $limit = 50): array {
    // enforce limit boundaries, so it's not tainted input
    $limit = max(1, min(100, $limit));

    if ($protected) {
      $stmt = $this->pdo->prepare(
        "SELECT id, username, is_admin FROM users WHERE username LIKE :q ORDER BY id LIMIT $limit"
      );
      $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      // vulnerable on purpose
      $sql = "SELECT id, username, is_admin FROM users WHERE username LIKE '%" . $q . "%' ORDER BY id LIMIT " . (int)$limit;
      $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    $users = [];
    foreach ($rows as $row) {
      $users[] = new User(
        id: (int)$row['id'],
        username: (string)$row['username'],
        isAdmin: (bool)((int)$row['is_admin'])
      );
    }
    return $users;
  }

  public function isAdmin(int $userId): bool {
    $stmt = $this->pdo->prepare('SELECT is_admin FROM users WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return false;
    }
    return (bool)((int)$row['is_admin']);
  }


}