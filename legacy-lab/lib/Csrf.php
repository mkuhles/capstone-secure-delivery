<?php
declare(strict_types=1);

final class Csrf
{
    public function __construct(
        private readonly Session $session,
        private readonly bool $enabled = true,
        private readonly string $sessionKey = '_csrf',
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

        $bag = $this->session->get($this->sessionKey, []);
        if (!is_array($bag)) {
            $bag = [];
        }

        if (empty($bag[$id])) {
            $bag[$id] = bin2hex(random_bytes(32));
            $this->session->set($this->sessionKey, $bag);
        }

        return (string)$bag[$id];
    }

    public function validate(string $id, ?string $token): bool
    {
        if (!$this->enabled) {
            return true; // demo mode: treat as valid
        }

        $bag = $this->session->get($this->sessionKey, []);
        $expected = is_array($bag) ? ($bag[$id] ?? '') : '';

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

        $bag = $this->session->get($this->sessionKey, []);
        if (!is_array($bag)) {
            $bag = [];
        }

        $bag[$id] = bin2hex(random_bytes(32));
        $this->session->set($this->sessionKey, $bag);
    }
}
