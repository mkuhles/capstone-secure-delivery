<?php
declare(strict_types=1);

/** @var \LegacyLab\Entities\Note $note */
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Legacy Lab - Note</title>
        <link rel="stylesheet" href="/assets/styles.css">
    </head>
<body>
    <p><a href="/index.php">Home</a></p>

    <h1>Note #<?= htmlspecialchars((string)$note->getId(), ENT_QUOTES, 'UTF-8') ?></h1>

    <!-- message block -->
    <?php if ($message !== null): ?>
        <h2>Messages</h2>
        <p class="message message--<?= $messageType ?? 'info' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($user): ?>
        <strong>Logged in as</strong> <code><?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?> (<?= $user->getId() ?>)</code>
        (admin: <?= ((int)$user->isAdmin() === 1)? 'yes' : 'no' ?>)
    <?php endif; ?>

    <p><strong>Owner user id:</strong>
        <?= htmlspecialchars((string)$note->getOwnerUserId(), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p><strong>Created:</strong>
        <?= htmlspecialchars($note->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <form method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf->token('note_edit'), ENT_QUOTES, 'UTF-8') ?>">
        <textarea name="content" rows="6" cols="60"><?= htmlspecialchars($note->getContent(), ENT_QUOTES, 'UTF-8') ?></textarea>
        <button type="submit">Save</button>
    </form>

</body>
</html>