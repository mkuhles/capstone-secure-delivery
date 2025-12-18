<?php
declare(strict_types=1);

final class Csrf
{
    public function __construct(
        private readonly bool $enabled = true,
        private readonly string $sessionKey = '_csrf'
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function token(string $id): string
    {
        if (!$this->enabled) {
            return '';
        }

        $this->ensureSession();
        $_SESSION[$this->sessionKey] ??= [];

        if (empty($_SESSION[$this->sessionKey][$id])) {
            $_SESSION[$this->sessionKey][$id] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[$this->sessionKey][$id];
    }

    public function validate(string $id, ?string $token): bool
    {
        if (!$this->enabled) {
            return true; // demo mode: treat as valid
        }

        $this->ensureSession();
        $expected = $_SESSION[$this->sessionKey][$id] ?? '';

        if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($expected, $token);
    }

    /**
     * rotate token after successful use (one-time token style)
     * may cause UX issues if user submits the same form twice or has multiple tabs open
     */
    public function rotate(string $id): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->ensureSession();
        $_SESSION[$this->sessionKey] ??= [];
        $_SESSION[$this->sessionKey][$id] = bin2hex(random_bytes(32));
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
