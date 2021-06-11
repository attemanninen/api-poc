<?php

namespace App\Test;

use Exception;

trait ApiResponseAssertationsTrait
{
    protected function assertResponseAttributes(array $attributes): void
    {
        if (!$this->client) {
            throw new Exception('Client not found!');
        }

        $content = $this->client->getResponse()->getContent();
        $responseAttributes = array_keys(json_decode($content, true));

        $this->assertSame($attributes, $responseAttributes);
    }
}
