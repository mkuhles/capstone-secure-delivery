<?php
declare(strict_types=1);
// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz, CSRF protected

[$container, $requestId] = require __DIR__ . '/_bootstrap.php';
$csrf = $container->csrf();
$auth = $container->auth();

// logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header('Location: /index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$csrf->validate('login', $_POST['_csrf'] ?? null)) {
    http_response_code(403);
    echo "Forbidden (CSRF)";
    exit;
  } else {
    $csrf->rotate('login');
  
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Missing credentials';
    } else {
        $user = $auth->attemptLogin($username, $password);

        // Constant-ish behavior: verify only if user exists
        if ($user) {
          $auth->login((int)$user->getId());

          header('Location: /index.php');
          exit;
        }

        $error = 'Invalid credentials';
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Legacy Lab - Login</title>
</head>
<body>
  <h1>Login (insecure)</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <!-- INSECURE: no CSRF token -->
  <form method="post" action="/login.php">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf->token('login'), ENT_QUOTES, 'UTF-8') ?>">
    <label>
      Username:
      <input name="username" autocomplete="username">
    </label><br>
    <label>
      Password:
      <input name="password" type="password" autocomplete="current-password">
    </label><br>
    <button type="submit">Login</button>
  </form>

  <p>Seed users (from config.php): <code>user/user</code> and <code>admin/admin</code></p>
  <p><a href="/index.php">Back</a></p>
</body>
</html>
