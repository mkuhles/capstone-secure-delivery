<?php
declare(strict_types=1);
namespace LegacyLab\Core;

use JsonException;

final class Logger {
    public function __construct(
        private readonly string $file,
        private readonly RequestId $requestId,
        private readonly bool $structured = true,
        private readonly bool $logInjectionProtected = true,
    ) {}

    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $ctx = $this->sanitizeContext($context);

        $base = [
            'timestamp'  => gmdate('c'),
            'level'      => $level,
            'message'    => $this->protect($message),
            'request_id' => $this->requestId->id(),
            'method'     => $_SERVER['REQUEST_METHOD'] ?? null,
            'path'       => $_SERVER['REQUEST_URI'] ?? null,
            'context'    => $ctx['sanitized'],
        ];

        try {
            if ($this->structured) {
                $line = json_encode($base, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            } else {
                // intentionally weaker mode (for demo)
                // still optionally protected depending on flag
                $weakMsg = $this->logInjectionProtected ? $this->protect($message) : $message;
                $weakCtx = $this->logInjectionProtected ? $ctx['sanitized'] : $context;

                $line = '[' . $base['timestamp'] . '] '
                . $level
                . ' rid=' . $base['request_id']
                . ' ' . $weakMsg
                . ' ' . json_encode($weakCtx, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } catch (JsonException $e) {
            // Guaranteed minimal, structured fallback (no user-controlled newlines)
            $fallback = [
                'timestamp'   => gmdate('c'),
                'level'       => 'error',
                'message'     => 'logger_json_encode_failed',
                'request_id'  => $this->requestId->id(),
                'error_class' => get_class($e),
                'error'       => $e->getMessage(),
                'context_keys' => $ctx['keys'],
                'context_types' => $ctx['types'],
                'context_hash' => $ctx['hash'],
            ];

            $line = json_encode($fallback, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if (!is_string($line)) {
                $line = '{"timestamp":"' . gmdate('c') . '","level":"error","message":"logger_json_encode_failed_hardly","request_id":"' . $this->requestId->id() . '"}';
            }
        }

        file_put_contents($this->file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function sanitizeContext(array $context): array{
        $superSensitiveKeys = [
            'password','passwd','pwd',
            'token','access_token','refresh_token','id_token',
            'authorization','cookie','set-cookie',
            'session','session_id','phpsessid',
            'api_key','apikey','secret','client_secret',
            'csrf','csrf_token',
        ];
        $sensitiveKeys = [
            'email','username',
        ];

        $sanitized = [];
        foreach ($context as $k => $v) {
            $key = (string)$k;
            $lower = strtolower($key);

            $isSuperSensitive = in_array($lower, $superSensitiveKeys, true);
            if ($isSuperSensitive) {
                continue;
            }

            $isSensitive = in_array($lower, $sensitiveKeys, true);
            if ($isSensitive) {
                $sanitized[$key] = '__REDACTED__';
                continue;
            }

            $sanitized[$key] = $this->normalizeForLog($v);
        }

        // limit size / cardinality
        ksort($sanitized);
        $sanitized = array_slice($sanitized, 0, 30, true);

        $keys = array_keys($sanitized);
        $types = [];
        foreach ($sanitized as $k => $v) {
            $types[$k] = gettype($v);
        }

        $hash = hash('sha256', json_encode($sanitized) ?: '');

        return [
            'sanitized' => $sanitized,
            'keys'      => $keys,
            'types'     => $types,
            'hash'      => $hash,
        ];
    }

    private function normalizeForLog(mixed $v): mixed
    {
        if ($v === null || is_bool($v) || is_int($v) || is_float($v)) {
            return $v;
        }

        if (is_string($v)) {
            $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $v) ?? '';
            return mb_substr($s, 0, 200);
        }

        if (is_array($v)) {
            $out = [];
            $i = 0;
            foreach ($v as $k => $vv) {
                $out[(string)$k] = $this->normalizeForLog($vv);
                if (++$i >= 20) break;
            }
            return $out;
        }

        if (is_object($v)) {
            return '__OBJECT__:' . get_class($v);
        }

        return '__NONSCALAR__';
    }

    private function protect(string $s): string
{
    if (!$this->logInjectionProtected) {
        return $s;
    }

    // remove control chars (CR/LF etc.)
    return preg_replace('/[\x00-\x1F\x7F]/u', '', $s) ?? '';
}

}