<?php
declare(strict_types=1);
namespace LegacyLab\Entities;

final class User {
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly bool $isAdmin
    ) {}
    
    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function isAdmin(): bool {
        return $this->isAdmin;
    }
}