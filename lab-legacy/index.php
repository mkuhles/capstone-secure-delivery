<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY)
session_start();

$user = $_SESSION['user'] ?? null;
$isAdmin = (bool)($_SESSION['is_admin'] ?? false);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Legacy Lab - Home</title>
</head>
<body>
  <h1>Legacy Lab</h1>

  <p><strong>Status:</strong>
    <?php if ($user): ?>
      Logged in as <code><?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?></code>
      (admin: <?= $isAdmin ? 'yes' : 'no' ?>)
    <?php else: ?>
      Not logged in
    <?php endif; ?>
  </p>

  <ul>
    <li><a href="/login.php">Login</a></li>
    <li><a href="/admin.php">Admin page</a></li>
    <li><a href="/login.php?logout=1">Logout</a></li>
  </ul>

  <hr>

  <h2>Notes (insecure demo)</h2>
  <p>This lab will later demonstrate: missing CSRF checks, session fixation, weak auth logic.</p>

  <?php if (!empty($_SESSION['admin_note'])): ?>
    <p><strong>Admin note:</strong> <?= $_SESSION['admin_note'] /* intentionally NOT escaped */ ?></p>
  <?php endif; ?>

</body>
</html>
