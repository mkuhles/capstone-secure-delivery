<?php
declare(strict_types=1);

use LegacyLab\Core\Container;
use LegacyLab\Core\Csrf;
use LegacyLab\Entities\Note;
use LegacyLab\Entities\User;
use LegacyLab\Repositories\NoteRepository;

$container = require __DIR__ . '/_bootstrap.php';

$auth = $container->auth();
$user = $auth->user();

if (!$user) {
    header('Location: /login.php');
    exit;
}

$pdo = $container->pdo(); // <-- adjust if needed
$notes = $container->notesRepository();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    show_all_notes($notes, $user);
    exit;
}

$note = $notes->findById($id);
if (!$note) {
    http_response_code(404);
    show_all_notes($notes, $user, "error", "Note not found");
    exit;
}

// IDOR
// Authorization: owner or admin
$idorProtected = (bool)($container->config('idor_protected') ?? true);
if ($idorProtected) {
    if ($note->getOwnerUserId() !== (int)$user->getId() && !$user->isAdmin()) {
        http_response_code(404);
        show_all_notes($notes, $user, "error", "Note not found");
        exit;
    }
}

// handle edit submission
$csrf = $container->csrf();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContent = trim((string)($_POST['content'] ?? ''));
    if ($newContent === '') {
        show_note($csrf, $note, $user, "error", "Content must not be empty");
        exit;
    }

    if (!$csrf->validate('note_edit', $_POST['_csrf'] ?? null)) {
        http_response_code(403);
        show_note($csrf, $note, $user, "error", "Forbidden (CSRF)");
        exit;
    }
    $csrf->rotate('note_edit');

    $notes->updateContent($note->getId(), $newContent);
    header('Location: /notes.php?id=' . $note->getId());
    exit;
}

// default: show note
show_note($csrf, $note, $user);


function show_note(Csrf $csrf, Note $note, User $user, ?string $messageType = null, ?string $message = null): void {
    require __DIR__ . '/../views/note_show.php';
}

function show_all_notes(NoteRepository $notesRepo, User $user, ?string $messageType = null, ?string $message = null): void {
    $notes = $notesRepo->listByOwner((int)$user->getId(), 20);
    require __DIR__ . '/../views/notes_list.php';
}