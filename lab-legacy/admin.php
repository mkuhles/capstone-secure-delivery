<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY)
session_start();

$user = $_SESSION['user'] ?? null;
$isAdmin = (bool)($_SESSION['is_admin'] ?? false);

// --- INSECURE AUTHZ: allow "admin" via query param (privilege escalation demo) ---
if (isset($_GET['force_admin']) && $_GET['force_admin'] === '1') {
    $_SESSION['is_admin'] = true; // DO NOT DO THIS IN REAL APPS
    $isAdmin = true;
}

// Very weak "gate"
if (!$user) {
    header('Location: /login.php');
    exit;
}

// --- INSECURE STATE CHANGE: no CSRF protection ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // even worse: does not check admin properly
    $_SESSION['admin_note'] = (string)($_POST['note'] ?? '');
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

  <p>Logged in as: <code><?= htmlspecialchars((string)$user, ENT_QUOTES, 'UTF-8') ?></code></p>
  <p>Admin flag: <strong><?= $isAdmin ? 'YES' : 'NO' ?></strong></p>

  <ul>
    <li><a href="/index.php">Home</a></li>
    <li><a href="/admin.php?force_admin=1">INSECURE: force admin via URL</a></li>
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
