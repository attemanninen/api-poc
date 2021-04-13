<?php

namespace App\Exception;

interface AdditionalDetailsExceptionInterface
{
    /**
     * Get description.
     *
     * @return ?string
     */
    public function getDescription(): ?string;

    /**
     * Get more error details.
     *
     * @return array
     */
    public function getErrors(): array;
}
