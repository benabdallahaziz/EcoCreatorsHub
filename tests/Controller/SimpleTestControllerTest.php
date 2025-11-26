<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SimpleTestControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
          $client->request('GET', '/simple/test');

        self::assertResponseIsSuccessful();
    }
}
