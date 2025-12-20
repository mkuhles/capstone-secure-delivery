<?php
declare(strict_types=1);

namespace LegacyLab\Core;

final class Cors
{
    /**
     * @param bool $corsProtected
     * @param string[] $allowedOrigins
     * @param bool $allowCredentials
     */
    public function __construct(
        private readonly bool $corsProtected,
        private readonly array $allowedOrigins = [],
        private readonly bool $allowCredentials = false,
    ) {}

    public function handle(): void
    {
        // Protected mode:
        if ($this->corsProtected) {
            // 1) Read request Origin header
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

            if($origin === '') {
                // No Origin header present
                return;
            }
            // 2) If Origin is in allowlist: set Access-Control-Allow-Origin: <origin>
            if (in_array($origin, $this->allowedOrigins, true)) {
                header('Access-Control-Allow-Origin: ' . ($origin));
                header('Vary: Origin');
            } elseif (($_SERVER['REQUEST_METHOD'] === 'OPTIONS')) {
                // Origin not allowed
                http_response_code(403);
                echo "Forbidden - CORS origin denied";
                exit;
            }
            // 3) credentials erlauben
            if ($this->allowCredentials) {
                header('Access-Control-Allow-Credentials: true');
            }
            // 4) preflight
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                header('Access-Control-Allow-Methods: POST, OPTIONS');
                header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');
                header('Access-Control-Max-Age: 600');
                http_response_code(204);
                exit;
            }
            return;
        }

        // Insecure mode:
        // 1) Read request Origin header
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        // 2) If Origin is in allowlist: set Access-Control-Allow-Origin: <origin>
        if($origin !== '') {
            header('Access-Control-Allow-Origin: ' . ($origin));
            header('Vary: Origin');
        }
        // 3) credentials erlauben
        header('Access-Control-Allow-Credentials: true');
        // 4) preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');
            header('Access-Control-Max-Age: 86400');
            http_response_code(204);
            exit;
        }
    }
}
