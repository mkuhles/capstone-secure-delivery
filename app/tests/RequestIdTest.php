<?php
declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RequestIdTest extends WebTestCase
{
    public function testResponseContainsXRequestId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $response = $client->getResponse();

        self::assertTrue(
            $response->headers->has('X-Request-ID'),
            'Response should contain X-Request-ID header'
        );

        $rid = (string) $response->headers->get('X-Request-ID');
        self::assertNotSame('', $rid, 'X-Request-ID must not be empty');

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $rid,
            'X-Request-ID must be a UUID'
        );
    }

    public function testAcceptsIncomingRequestIdFromTrustedProxy(): void
    {
        $client = static::createClient();

        // Simulate request coming from a trusted proxy IP (matches TRUSTED_PROXIES)
        $client->setServerParameter('REMOTE_ADDR', '127.0.0.1');

        $uuid = '11111111-2222-4333-8444-555555555555';
        $client->request('GET', '/', [], [], ['HTTP_X_REQUEST_ID' => $uuid]);

        $response = $client->getResponse();
        self::assertSame($uuid, $response->headers->get('X-Request-ID'));
    }

    public function testIgnoresIncomingRequestIdFromUntrustedProxy(): void {
        $client = static::createClient();

        // Simulate untrusted client IP (NOT in TRUSTED_PROXIES)
        $client->setServerParameter('REMOTE_ADDR', '10.10.10.10');

        $incoming = '11111111-2222-4333-8444-555555555555';

        $client->request(
            'GET',
            '/',
            [],
            [],
            ['HTTP_X_REQUEST_ID' => $incoming]
        );

        $response = $client->getResponse();

        self::assertTrue($response->headers->has('X-Request-ID'));

        $returned = (string) $response->headers->get('X-Request-ID');

        // Must NOT trust incoming ID
        self::assertNotSame($incoming, $returned);

        // Must still be a valid UUID
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $returned
        );
    }

}
