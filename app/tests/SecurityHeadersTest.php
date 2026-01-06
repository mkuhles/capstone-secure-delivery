<?php
declare(strict_types=1);

namespace App\Tests;

use App\Tests\BaseTestCase;

final class SecurityHeadersTest extends BaseTestCase
{
    public function testSecurityHeadersArePresentOverHttps(): void
    {
        $client = $this->httpsClient();
        $client->request('GET', '/');

        $response = $client->getResponse();

        // HSTS (only meaningful over HTTPS)
        self::assertTrue(
            $response->headers->has('Strict-Transport-Security'),
            'Expected Strict-Transport-Security header over HTTPS'
        );

        $hsts = (string) $response->headers->get('Strict-Transport-Security');
        self::assertStringContainsString('max-age=', $hsts);

        // CSP: could be enforced or report-only depending on config
        $hasCsp = $response->headers->has('Content-Security-Policy')
            || $response->headers->has('Content-Security-Policy-Report-Only');

        self::assertTrue($hasCsp, 'Expected CSP header (enforced or report-only)');

        // Optional: baseline hardening headers (if configured)
        if ($response->headers->has('X-Content-Type-Options')) {
            self::assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        }

        if ($response->headers->has('X-Frame-Options')) {
            self::assertNotSame('', (string) $response->headers->get('X-Frame-Options'));
        }
    }
}
