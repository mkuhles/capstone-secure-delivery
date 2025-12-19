<?php
declare(strict_types=1);
namespace LegacyLab\Core;

use LegacyLab\Core\Csrf;
use LegacyLab\Core\Database;
use LegacyLab\Core\Session;
use LegacyLab\Secrurity\Auth;
use LegacyLab\Repositories\UserRepository;
use LegacyLab\Repositories\AdminNoteRepository;
use PDO;

final class Container
{
    private array $services = [];

    public function __construct(private readonly array $config) {}

    public function config(?string $key = null): mixed
    {
        if ($key !== null) {
            return $this->config[$key] ?? null;
        }
        return $this->config;
    }

    public function pdo(): PDO
    {
        return $this->services['pdo'] ??= (new Database($this->config['db_file']))->pdo();
    }

    public function session(): Session
    {
        $s = $this->services['session'] ??= new Session();
        $s->start();
        return $s;
    }

    public function csrf(): Csrf
    {
        return $this->services['csrf'] ??= new Csrf(
            $this->session(),
            (bool)($this->config['csrf_protected'] ?? true)
        );
    }

    public function users(): UserRepository
    {
        return $this->services['users'] ??= new UserRepository($this->pdo());
    }

    public function auth(): Auth
    {
        return $this->services['auth'] ??= new Auth($this->pdo(), $this->session(), $this->users());
    }

    public function notes(): AdminNoteRepository
    {
        return $this->services['notes'] ??= new AdminNoteRepository($this->pdo());
    }

    public function xss(): XSS
    {
        return $this->services['xss'] ??= new XSS(
            (bool)($this->config['xss_protected'] ?? true)
        );
    }
}
