<?php

namespace App\Tests\Controller;

use App\Test\AuthenticatedClientTrait;
use App\Test\DatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use AuthenticatedClientTrait;
    use DatabaseTrait;

    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->loadFixtures(['company', 'user', 'task', 'customer']);
        $this->authenticateUser('user-1');
    }

    protected function tearDown(): void
    {
        $this->client = null;
        parent::tearDown();
    }

    public function testMe(): void
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }
}
