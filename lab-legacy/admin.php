<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY)
session_start();

// CSRF token for "admin_note" form
if (empty($_SESSION['csrf_admin_note'])) {
    $_SESSION['csrf_admin_note'] = bin2hex(random_bytes(32));
}

$user = $_SESSION['user'] ?? null;
$isAdmin = (bool)($_SESSION['is_admin'] ?? false);

// Very weak "gate"
if (!$user) {
    header('Location: /login.php');
    exit;
}

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // check CSRF token
    $token = (string)($_POST['_csrf'] ?? '');
    $expected = (string)($_SESSION['csrf_admin_note'] ?? '');

    if ($expected === '' || !hash_equals($expected, $token)) {
        http_response_code(403);
        echo "Forbidden (CSRF)";
        exit;
    }

    // rotate token after successful use (one-time token style)
    $_SESSION['csrf_admin_note'] = bin2hex(random_bytes(32));


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
  </ul>

  <hr>

  <h2>Set admin note (insecure state change)</h2>
  <p>This POST has <strong>no CSRF token</strong>.</p>

  <form method="post" action="/admin.php">
    <textarea name="note" rows="3" cols="60" placeholder="This will be shown on /index.php"></textarea><br>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_admin_note'], ENT_QUOTES, 'UTF-8') ?>">

    <button type="submit">Save note</button>
  </form>

  <p><small>Later we’ll demonstrate how a third-party site could trigger this POST.</small></p>
</body>
</html>
