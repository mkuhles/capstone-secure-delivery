<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NoteControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/note');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), 'Index page should be accessible.');
        self::assertResponseIsSuccessful();
    }
}
