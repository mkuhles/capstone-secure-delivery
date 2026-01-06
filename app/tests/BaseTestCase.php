<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    protected function httpsClient()
    {
        $client = static::createClient();
        $client->setServerParameter('HTTPS', 'on');
        $client->setServerParameter('HTTP_HOST', 'localhost');
        return $client;
    }
}
