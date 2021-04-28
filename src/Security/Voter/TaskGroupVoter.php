<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\GroupRoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskGroupVoter extends Voter
{
    /**
     * @var array
     */
    public const ATTRIBUTE_TO_GROUP_ROLE_MAP = [
        'view' => 'ROLE_GROUP_TASK_VIEW',
        'add' => 'ROLE_GROUP_TASK_ADD',
        'edit' => 'ROLE_GROUP_TASK_EDIT',
        'delete' => 'ROLE_GROUP_TASK_DELETE'
    ];

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject)
    {
        if (!isset(self::ATTRIBUTE_TO_GROUP_ROLE_MAP[$attribute])) {
            return false;
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

        foreach ($subject->getGroups() as $taskGroup) {
            foreach ($user->getGroupRoles() as $groupRole) {
                if ($groupRole->getGroup() === $taskGroup->getGroup()) {
                    return $groupRole->hasRole(self::ATTRIBUTE_TO_GROUP_ROLE_MAP[$attribute]);
                }
            }
        }

        return false;
    }
}
