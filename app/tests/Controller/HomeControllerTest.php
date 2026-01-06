<?php

namespace App\Tests\Controller;

use App\Tests\BaseTestCase;

final class HomeControllerTest extends BaseTestCase
{
    public function testIndex(): void
    {
        $client = $this->httpsClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
