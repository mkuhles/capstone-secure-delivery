<?php

namespace App\Security;

final class RequestId {
    public const HEADER_PRIMARY = 'X-Request-ID-blah';
    public const HEADER_FALLBACK = 'X-Correlation-ID';

    private string $id;

    public function getId(): string {
        return $this->id;
    }

    public function resolve(?string $incoming, bool $trusted): string {
        if ($trusted) {
            $normalized = $this->normalizeUuid($incoming);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return self::uuidV4();
    }

    private function normalizeUuid(?string $raw): ?string {
        if ($raw === null) return null;
        $raw = trim($raw);
        if ($raw === '' || strlen($raw) > 128) return null;

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $raw)) {
            return strtolower($raw);
        }

        return null;
    }

    public static function uuidV4(): string {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}