<?php

namespace App\Test;

use App\Repository\UserRepository;
use Exception;

trait AuthenticatedClientTrait
{
    protected function authenticateUser(string $username): void
    {
        if (!$this->client) {
            throw new Exception('Client not found!');
        }

        $user = static::$container
            ->get(UserRepository::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            throw new Exception('User not found!');
        }

        $this->client->setServerParameters([
            'HTTP_Authorization' => 'Bearer ' . $user->getApiKey(),
        ]);
    }
}
