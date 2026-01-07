<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz, CSRF protected

$container = require __DIR__ . '/_bootstrap.php';
$csrf = $container->csrf();
$auth = $container->auth();
$adminNoteRepo = $container->adminNotesRepository();

$user = $auth->user();
if (!$user || !$user->isAdmin()) {
    http_response_code(403);
    echo "Forbidden <a href=\"/index.php\">Go back</a>";
    exit;
}

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // check CSRF token
  if (!$csrf->validate('admin_note', $_POST['_csrf'] ?? null)) {
    http_response_code(403);
    echo "Forbidden (CSRF)";
    exit;
  }
  $csrf->rotate('admin_note');

  $note = (string)($_POST['note'] ?? '');
  $adminNoteRepo->create($note, (int)$user->getId());

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
  <h1>Admin (CSRF-protected)</h1>

  <p>Logged in as: <code><?= htmlspecialchars((string)$user->getUsername(), ENT_QUOTES, 'UTF-8') ?></code></p>
  <p>Admin flag: <strong><?= ((int)$user->isAdmin() === 1)? 'YES' : 'NO' ?></strong></p>

  <ul>
    <li><a href="/index.php">Home</a></li>
    <li><a href="/login.php?logout=1">Logout</a></li>
  </ul>

  <hr>

  <h2>Set admin note</h2>
  <p>This POST is now protected against CSRF.</p>

  <form method="post" action="/admin.php">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf->token('admin_note'), ENT_QUOTES, 'UTF-8') ?>">
    <textarea name="note" rows="3" cols="60" placeholder="This will be shown on /index.php"></textarea><br>
    <button type="submit">Save note</button>
  </form>
  
  <p><small>Later we’ll demonstrate how a third-party site could trigger this POST.</small></p>
</body>
</html>
