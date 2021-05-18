<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\TeamPermission;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskTeamVoter extends Voter
{
    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject)
    {
        // Hmm... This is not pretty.
        $permission = 'task_' . $attribute;
        if ($permission === TeamPermission::TASK_VIEW
            || $permission === TeamPermission::TASK_CREATE
            || $permission === TeamPermission::TASK_EDIT
            || $permission === TeamPermission::TASK_REMOVE
        ) {
            return true;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
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

        if ($subject->getCompany() === $user->getCompany()) {
            return true;
        }

        $permission = 'task_' . $attribute;
        foreach ($subject->getTeams() as $taskTeam) {
            foreach ($user->getTeamPermissions() as $teamPermission) {
                if ($teamPermission->getTeam() === $taskTeam->getTeam()) {
                    return $teamPermission->hasPermission($permission);
                }
            }
        }

        return false;
    }
}
