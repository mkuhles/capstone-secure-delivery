<?php
declare(strict_types=1);

namespace LegacyLab\Core;

final class SecurityHeaders {
    /**
     * @return array{csp_nonce: string|null}
     */
    public function apply(array $config): array
    {
        $headers = $config['security_headers'] ?? [];

        if (!empty($headers['x_frame_options'])) {
            header('X-Frame-Options: DENY');
        }

        if (!empty($headers['x_content_type_options'])) {
            header('X-Content-Type-Options: nosniff');
        }

        $cspNonce = null;
        $csp = $headers['csp'] ?? null;

        if (is_array($csp) && !empty($csp['enabled'])) {
            $mode = $csp['mode'] ?? 'report-only';
            $scriptPolicy = $csp['script_policy'] ?? 'none';
            $stylePolicy = $csp['style_policy'] ?? 'none';

            if ($scriptPolicy === 'nonce') {
                $cspNonce = rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
            }

            $directives = [];
            $directives[] = 'default-src ' . implode(' ', $csp['default_src'] ?? ["'self'"]);

            $scriptSrc = ["'self'"];
            if ($scriptPolicy === 'unsafe-inline') {
                $scriptSrc[] = "'unsafe-inline'";
            } elseif ($scriptPolicy === 'nonce' && $cspNonce) {
                $scriptSrc[] = "'nonce-{$cspNonce}'";
            }
            $directives[] = 'script-src ' . implode(' ', $scriptSrc);

            $styleSrc = ["'self'"];
            if ($stylePolicy === 'unsafe-inline') {
                $styleSrc[] = "'unsafe-inline'";
            } elseif ($stylePolicy === 'nonce' && $cspNonce) {
                $styleSrc[] = "'nonce-{$cspNonce}'";
            }
            $directives[] = 'style-src ' . implode(' ', $styleSrc);

            $directives[] = 'img-src ' . implode(' ', $csp['img_src'] ?? ["'self'", 'data:']);
            $directives[] = 'connect-src ' . implode(' ', $csp['connect_src'] ?? ["'self'"]);
            $directives[] = 'object-src ' . implode(' ', $csp['object_src'] ?? ["'none'"]);
            $directives[] = 'base-uri ' . implode(' ', $csp['base_uri'] ?? ["'self'"]);
            $directives[] = 'frame-ancestors ' . implode(' ', $csp['frame_ancestors'] ?? ["'none'"]);

            $policy = implode('; ', $directives);

            $headerName = ($mode === 'enforce')
                ? 'Content-Security-Policy'
                : 'Content-Security-Policy-Report-Only';

            header($headerName . ': ' . $policy);
        }

        return ['csp_nonce' => $cspNonce];
    }
}
