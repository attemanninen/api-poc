<?php

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\TeamPermission;
use App\Entity\User;
use App\Repository\TeamPermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    /**
     * @var TeamPermissionRepository
     */
    private $teamPermissionRepository;

    public function __construct(TeamPermissionRepository $repository)
    {
        $this->teamPermissionRepository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject)
    {
        if (!$subject instanceof Team) {
            return false;
        }

        if ($attribute === 'view') {
            return true;
        }

        // Hmm... This is not pretty.
        $permission = 'team_' . $attribute;

        if ($permission === TeamPermission::TEAM_EDIT
            || $permission === TeamPermission::TEAM_REMOVE
        ) {
            return true;
        }

        return false;
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

        $teamPermission = $this->teamPermissionRepository->findOneBy([
            'team' => $subject,
            'user' => $user,
        ]);

        if ($teamPermission && $attribute === 'view') {
            return true;
        }

        if ($teamPermission && $teamPermission->hasPermission('team_' . $attribute)) {
            return true;
        }

        // TODO: ROLE_TEAM

        return $subject->getCompany() === $user->getCompany();
    }
}
