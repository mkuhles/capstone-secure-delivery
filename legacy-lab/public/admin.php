<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz
session_start();

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/auth.php';

$user = require_admin($pdo);

// --- INSECURE STATE CHANGE: no CSRF protection ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $note = (string)($_POST['note'] ?? '');

  $stmt = $pdo->prepare("
      INSERT INTO admin_notes (note, created_at, created_by_user_id)
      VALUES (:note, :created_at, :uid)
  ");
  $stmt->execute([
      ':note' => $note,
      ':created_at' => (new DateTimeImmutable('now'))->format(DateTimeInterface::ATOM),
      ':uid' => (int)$user['id'],
  ]);

  header('Location: /index.php');
  exit;
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Legacy Lab - Admin</title>
</head>
<body>
  <h1>Admin (insecure)</h1>

  <p>Logged in as: <code><?= htmlspecialchars((string)$user['username'], ENT_QUOTES, 'UTF-8') ?></code></p>
  <p>Admin flag: <strong><?= ((int)$user['is_admin'] === 1)? 'YES' : 'NO' ?></strong></p>

  <ul>
    <li><a href="/index.php">Home</a></li>
    <li><a href="/login.php?logout=1">Logout</a></li>
  </ul>

  <hr>

  <h2>Set admin note (insecure state change)</h2>
  <p>This POST has <strong>no CSRF token</strong>.</p>

  <form method="post" action="/admin.php">
    <textarea name="note" rows="3" cols="60" placeholder="This will be shown on /index.php"></textarea><br>
    <button type="submit">Save note</button>
  </form>
  
  <p><small>Later we’ll demonstrate how a third-party site could trigger this POST.</small></p>
</body>
</html>
