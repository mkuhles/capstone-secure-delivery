<?php
declare(strict_types=1);

final class Auth
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Session $session
    ) {}

    public function user(): ?array
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT id, username, is_admin FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int)$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function requireLogin(): array
    {
        $user = $this->user();
        if (!$user) {
            header('Location: /login.php');
            exit;
        }
        return $user;
    }

    public function requireAdmin(): array
    {
        $user = $this->requireLogin();
        if (!(bool)((int)$user['is_admin'])) {
            http_response_code(403);
            echo "Forbidden <a href=\"/index.php\">Go back</a>";
            exit;
        }
        return $user;
    }

    public function login(int $userId): void
    {
        $this->session->regenerate(true);
        $this->session->set('user_id', $userId);
    }

    public function logout(): void
    {
        $this->session->destroy();
    }
}
