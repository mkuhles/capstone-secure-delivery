<?php
declare(strict_types=1);
// Intentionally insecure legacy lab (LOCAL ONLY) - SQLi demo via config switch

use LegacyLab\Repositories\UserRepository;
use LegacyLab\Core\Container;

[$container, $requestId] = require __DIR__ . '/_bootstrap.php';

/** @var UsersRepository $usersRepo */
$usersRepo = $container->users();

// Use ?q=... as the search term
$q = (string)($_GET['q'] ?? '');
$sqliProtected = (bool)($container->config('sqli_protected') ?? true);

$results = [];
$error = null;

if ($q !== '') {
  try {
    $results = $usersRepo->searchByUsername($q, $sqliProtected, 50);
  } catch (Throwable $e) {
    // In a real app, you would not leak raw DB errors.
    $error = $e->getMessage();
  }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Legacy Lab - SQLi Search</title>
</head>
<body>
  <h1>SQLi demo: user search</h1>

  <p>
    <a href="/index.php">Home</a>
  </p>

  <p>
    <strong>SQLi protection:</strong>
    <?= $sqliProtected ? 'ON (prepared statements)' : 'OFF (string concatenation - vulnerable)' ?>
  </p>

  <form method="get" action="/search.php">
    <label>
      q (query):
      <input name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" size="40" autofocus>
    </label>
    <button type="submit">Search</button>
  </form>

  <hr>

  <?php if ($error !== null): ?>
    <p><strong>DB error:</strong> <code><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></code></p>
  <?php endif; ?>

  <?php if ($q === ''): ?>
    <p>Try a normal search like <code>user</code> or <code>admin</code>.</p>
    <p>
      When protection is OFF, you can demonstrate SQL injection with something like:<br>
      <code>%&#039; OR 1=1 -- </code>
    </p>
  <?php else: ?>
    <p>
      Results for <code><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></code>:
      <strong><?= count($results) ?></strong>
    </p>

    <ul>
      <?php foreach ($results as $u): ?>
        <li>
          <code><?= htmlspecialchars($u->getUsername(), ENT_QUOTES, 'UTF-8') ?></code>
          (admin: <?= ((int)$u->isAdmin() === 1) ? 'yes' : 'no' ?>)
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>

