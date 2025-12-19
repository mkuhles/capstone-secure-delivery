<?php
declare(strict_types=1);
namespace LegacyLab\Secrurity;

use LegacyLab\Core\Session;
use LegacyLab\Entities\User;
use LegacyLab\Repositories\UserRepository;
use PDO;

final class Auth
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly Session $session,
        private readonly UserRepository $userRepository
    ) {}

    public function user(): ?User
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        $user = (new UserRepository($this->pdo))->findById((int)$userId);

        return $user ?: null;
    }

    public function requireLogin(): ?User
    {
        $user = $this->user();
        if (!$user) {
            header('Location: /login.php');
            exit;
        }
        return $user;
    }

    public function requireAdmin(): ?User
    {
        $user = $this->requireLogin();
        if (!$user->isAdmin()) {
            http_response_code(403);
            echo "Forbidden <a href=\"/index.php\">Go back</a>";
            exit;
        }
        return $user;
    }

    public function login(int $userId): void
    {
        //prevent session fixation
        $this->session->regenerate(true);
        $this->session->set('user_id', $userId);
    }

    public function logout(): void
    {
        $this->session->destroy();
    }

    public function attemptLogin(string $username, string $password): ?User
    {
        $user = $this->userRepository->verifyPassword($username, $password);
        return $user ?: null;
    }
}
