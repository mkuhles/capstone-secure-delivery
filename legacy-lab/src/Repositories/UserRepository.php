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