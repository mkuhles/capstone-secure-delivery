<?php
declare(strict_types=1);
// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz, CSRF protected

require __DIR__ . '/../vendor/autoload.php';
use LegacyLab\Core\Bootstrap;

$container = Bootstrap::container();
$auth = $container->auth();
$user = $auth->user();

$usersRepo = $container->users();
$adminNoteRepo = $container->notes();

$xss = $container->xss();

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
      Logged in as <code><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></code>
      (admin: <?= ((int)$user->isAdmin() === 1)? 'yes' : 'no' ?>)
    <?php else: ?>
      Not logged in
    <?php endif; ?>
  </p>

  <ul>
    <li><a href="/login.php">Login</a></li>
    <?php if ($user): ?><li><a href="/login.php?logout=1">Logout</a></li><?php endif; ?>
    <li><a href="/admin.php">Admin page</a></li>
    <li><a href="/search.php">SQLi search demo</a></li>
  </ul>

  <hr>

  <h2>Notes (insecure demo)</h2>
  <p>This lab may demonstrate missing CSRF checks.</p>

  <?php
  $adminNotes = $adminNoteRepo->latestNotes(10);
  ?>

  <p><strong>admin notes:</strong>
    <ul>
      <?php foreach ($adminNotes as $note):
        $noteUser = $usersRepo->findById($note->getCreatedByUserId());

        ?>
        <li>
          <?= $xss->output($note->getNote()) ?>
          <small>by <?= htmlspecialchars($noteUser->getUsername(), ENT_QUOTES, 'UTF-8') ?> at <?= htmlspecialchars($note->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?></small>
        </li>
      <?php endforeach; ?>
    </ul>
  </p>

</body>
</html>
