<?php
declare(strict_types=1);

// Intentionally insecure legacy lab (LOCAL ONLY)

// --- INSECURE: session fixation demo ---
// Allows setting the session id via URL parameter.
if (isset($_GET['sid'])) {
    session_id((string)$_GET['sid']); // DO NOT DO THIS IN REAL APPS
}

session_start();

// logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: /index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string)($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    // --- INSECURE AUTH LOGIC ---
    // Hardcoded credentials, no rate limiting, no proper password hashing.
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['user'] = 'admin';
        $_SESSION['is_admin'] = true;
        header('Location: /admin.php');
        exit;
    }

    if ($username === 'user' && $password === 'user') {
        $_SESSION['user'] = 'user';
        $_SESSION['is_admin'] = false;
        header('Location: /index.php');
        exit;
    }

    $error = 'Invalid credentials';
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

  <p>Try: <code>admin/admin</code> or <code>user/user</code></p>

  <p><a href="/index.php">Back</a></p>
</body>
</html>
