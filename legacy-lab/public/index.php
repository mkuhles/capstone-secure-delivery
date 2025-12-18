<?php
declare(strict_types=1);

require __DIR__ . '/../lib/bootstrap.php';
require __DIR__ . '/../lib/auth.php';

$user = current_user($pdo);

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
      Logged in as <code><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></code>
      (admin: <?= ((int)$user['is_admin'] === 1)? 'yes' : 'no' ?>)
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

  <?php
  $stmt = $pdo->query("
    SELECT an.note, an.created_at, u.username
    FROM admin_notes an
    JOIN users u ON u.id = an.created_by_user_id
    ORDER BY an.id DESC
  ");
  ?>

  <p><strong>admin notes:</strong>
    <ul>
      <?php while($latest = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <li>
          <?= $latest['note'] /* intentionally NOT escaped (legacy demo) */ ?>
          <small>by <?= htmlspecialchars($latest['username'], ENT_QUOTES, 'UTF-8') ?> at <?= htmlspecialchars($latest['created_at'], ENT_QUOTES, 'UTF-8') ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  </p>

</body>
</html>
