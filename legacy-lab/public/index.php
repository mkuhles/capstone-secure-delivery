<?php
declare(strict_types=1);
// Intentionally insecure legacy lab (LOCAL ONLY) - DB-backed authz, CSRF protected

$container = require __DIR__ . '/_bootstrap.php';
$auth = $container->auth();
$user = $auth->user();

$usersRepo = $container->users();
$adminNoteRepo = $container->adminNotesRepository();

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
    <li><a href="/notes.php">My Notes</a></li>
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

  <h2>W2D3 test CSP</h2>
  <div id="csp-test-div"></div>
<?php 
  $cspNonce = $container->cspNonce();
  $nonceText = 'without nonce';
?>
  <?php if (is_string($cspNonce) && $cspNonce !== ''): 
    $nonceText = 'with nonce'; ?>
    <p>CSP Nonce is set to: <code><?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?></code></p>
  <?php else:  $cspNonce = ''; ?>
    <p>CSP Nonce is not set.</p>
  <?php endif; ?>
  <script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    // get #csp-test-div and append 'js is working' text
    const div = document.getElementById('csp-test-div');
    div.innerHTML += ' - JS is working <?= $nonceText ?>.';
  </script>
  <style nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    #csp-test-div {
      color: green;
      font-weight: bold;
    }
  </style>
  <img src="https://picsum.photos/200" alt="random image from picsum.photos">

</body>
</html>
