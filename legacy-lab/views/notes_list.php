<?php
declare(strict_types=1);

/** @var \LegacyLab\Entities\Note $note */
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Legacy Lab - My Notes</title>
        <link rel="stylesheet" href="/assets/styles.css">
    </head>
    <body>
        <p><a href="/index.php">Home</a></p>

        <h1>My Notes</h1>

        <!-- message block -->
        <?php if ($message !== null): ?>
            <h2>Messages</h2>
            <p class="message message--<?= $messageType ?? 'info' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <h2>List of notes</h2>
        <ul>
            <?php foreach ($notes as $note): ?>
            <li>
                <a href="/notes.php?id=<?= htmlspecialchars((string)$note->getId(), ENT_QUOTES, 'UTF-8') ?>">
                Note #<?= htmlspecialchars((string)$note->getId(), ENT_QUOTES, 'UTF-8') ?>
                (created at <?= htmlspecialchars($note->getCreatedAt(), ENT_QUOTES, 'UTF-8') ?>)
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>