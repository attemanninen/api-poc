<?php

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject)
    {
        return $subject instanceof Team;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $subject->getCompany() === $user->getCompany();
    }
}
