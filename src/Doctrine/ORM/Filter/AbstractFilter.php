<?php

namespace App\Doctrine\ORM\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Filter\SQLFilter;
use ReflectionProperty;

/**
 * Inspiration from https://github.com/fxpio/fxp-doctrine-extensions/blob/master/Filter/AbstractFilter.php
 *
 * This feels a bit hacky to get the private entity manager and "real" parameters
 * (parameters without quotes) from parent.
 */
abstract class AbstractFilter extends SQLFilter
{
    private $entityManager;
    private $refParameters;

    protected function getEntityManager(): EntityManagerInterface
    {
        if (null === $this->entityManager) {
            $ref = new ReflectionProperty(SQLFilter::class, 'em');
            $ref->setAccessible(true);
            $this->entityManager = $ref->getValue($this);
        }

        return $this->entityManager;
    }

    protected function getRealParameter(string $name)
    {
        $this->getParameter($name);

        if (null === $this->refParameters) {
            $this->refParameters = new ReflectionProperty(SQLFilter::class, 'parameters');
            $this->refParameters->setAccessible(true);
        }

        $parameters = $this->refParameters->getValue($this);

        return $parameters[$name]['value'];
    }
}
