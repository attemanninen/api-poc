<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        static::$container
            ->get(DatabaseToolCollection::class)
            ->get()
            ->loadAliceFixture([
                __DIR__ . '/../../fixtures/company.yaml',
                __DIR__ . '/../../fixtures/user.yaml'
            ]);

        $user = static::$container
            ->get(UserRepository::class)
            ->findOneBy(['username' => 'user-1']);
        $this->client->setServerParameters([
            'HTTP_Authorization' => 'Bearer ' . $user->getApiKey()
        ]);
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }

    public function testMe(): void
    {
        $this->client->request('GET', '/me');
        $this->assertResponseIsSuccessful();
    }
}
