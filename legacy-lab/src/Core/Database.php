<?php
declare(strict_types=1);
namespace LegacyLab\Core;

use PDO;

final class Database {
  private ?PDO $pdo = null;

  public function __construct(private string $dbFile) {}
  public function pdo(): PDO {
    if ($this->pdo) return $this->pdo;
    
    $pdo = new PDO('sqlite:' . $this->dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');

    return $this->pdo = $pdo;
   }
}
