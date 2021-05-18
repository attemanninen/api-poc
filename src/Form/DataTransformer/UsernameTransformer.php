<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UsernameTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (user) to a string (username).
     *
     * @param  User|null $user
     */
    public function transform($user): string
    {
        if (null === $user) {
            return '';
        }

        return $user->getUsername();
    }

    /**
     * Transforms a string (username) to an object (user).
     *
     * @param  string $username
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($username): ?User
    {
        if (!$username) {
            return null;
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        ;

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                'An user with username "%s" does not exist!',
                $username
            ));
        }

        return $user;
    }
}
