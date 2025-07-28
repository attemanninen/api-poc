<?php

namespace App\DataFixtures\Processor;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserPasswordProcessor implements ProcessorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess(string $fixtureId, $object): void
    {
        if (false === $object instanceof User) {
            return;
        }

        $plainPassword = $object->getPlainPassword();

        if (!$plainPassword) {
            return;
        }

        $encodedPassword = $this->encoder->encodePassword($object, $plainPassword);
        $object->setPassword($encodedPassword);
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(string $fixtureId, $object): void
    {
        // Do nothing.
    }
}
