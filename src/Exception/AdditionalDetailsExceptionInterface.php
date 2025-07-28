<?php

namespace App\Exception;

interface AdditionalDetailsExceptionInterface
{
    /**
     * Get description.
     */
    public function getDescription(): ?string;

    /**
     * Get more error details.
     */
    public function getErrors(): array;
}
