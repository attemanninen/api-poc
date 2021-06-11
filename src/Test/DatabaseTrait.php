<?php

namespace App\Test;

use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

trait DatabaseTrait
{
    protected function loadFixtures(array $fixtureNames): void
    {
        if (!static::$container) {
            throw new Exception('Container not found! Forgot to boot kernel?');
        }

        $projectDir = static::$kernel->getProjectDir();
        $fixtures = array_map(
            fn ($fixtureName) => $projectDir . '/fixtures/' . $fixtureName . '.yaml',
            $fixtureNames
        );

        static::$container
            ->get(DatabaseToolCollection::class)
            ->get()
            ->loadAliceFixture($fixtures);
    }
}
