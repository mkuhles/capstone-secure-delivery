<?php
declare(strict_types=1);
namespace LegacyLab\Core;

final class RequestId {
    public const HEADER_PRIMARY = 'X-Request-ID';
    public const HEADER_FALLBACK = 'X-Correlation-ID';

    private string $id;

    public function id(): string {
        return $this->id;
    }

    /**
     * Reads incoming request id (prefer X-Request-ID, then X-Correlation-ID),
     * if proxy is trusted,
     * validates/normalizes it, generates a UUIDv4 if missing/invalid,
     * and always sets X-Request-ID on the response.
     */
    public function initFromGlobals(array $trustedProxies): string {
        $remoteAddr = (string)($_SERVER['REMOTE_ADDR'] ?? '');

        $incoming = null;
        if ($this->isTrustedProxy($remoteAddr, $trustedProxies)) {
            $incoming =
                $this->readHeader(self::HEADER_PRIMARY)
                ?? $this->readHeader(self::HEADER_FALLBACK);
        }

        $id = $this->normalizeOrNull($incoming) ?? self::uuidV4();
        $this->id = $id;

        header(self::HEADER_PRIMARY . ': ' . $id);
        return $id;
    }

    private function readHeader(string $name): ?string {
        // PHP built-in server: headers end up in $_SERVER['HTTP_<NAME>']
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        $val = $_SERVER[$key] ?? null;

        if (!is_string($val)) {
            return null;
        }

        $val = trim($val);
        return $val !== '' ? $val : null;
    }

    private function normalizeOrNull(?string $id): ?string {
        if ($id === null) {
            return null;
        }

        // Hard limits to avoid log abuse / header weirdness
        if ($id === '' || strlen($id) > 128) {
            return null;
        }

        // Simple validation: must be UUIDv4 format
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $id)) {
            return strtolower($id);
        }

        return null;
    }

    private static function uuidV4(): string {
        $data = random_bytes(16);
        // Set version to 0100
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        
        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function isTrustedProxy(string $remoteAddr, array $trustedProxies): bool {
        // Minimal version: exact match
        return in_array($remoteAddr, $trustedProxies, true);
    }

}