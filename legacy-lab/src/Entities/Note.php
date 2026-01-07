<?php
declare(strict_types=1);

namespace LegacyLab\Entities;

final class Note
{
    public function __construct(
        private int $id,
        private int $ownerUserId,
        private string $content,
        private string $createdAt
    ) {}

    public function getId(): int { return $this->id; }
    public function getOwnerUserId(): int { return $this->ownerUserId; }
    public function getContent(): string { return $this->content; }
    public function getCreatedAt(): string { return $this->createdAt; }
}
