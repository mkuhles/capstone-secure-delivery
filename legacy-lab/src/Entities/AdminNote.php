<?php
declare(strict_types=1);
namespace LegacyLab\Entities;

final class AdminNote {
  public function __construct(
    public readonly int $id,
    public readonly string $note,
    public readonly string $createdAt,
    public readonly int $createdByUserId
  ) {}

  public function getId(): int {
    return $this->id;
  }

    public function getNote(): string {
        return $this->note;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getCreatedByUserId(): int {
        return $this->createdByUserId;
    }
}