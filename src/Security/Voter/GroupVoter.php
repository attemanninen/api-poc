<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GroupVoter extends Voter
{
    /**
     * @var GroupRoleRepository
     */
    private $repository;

    public function __construct(GroupRoleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject)
    {
        if ($attribute !== 'view') {
            return false;
        }

        if (!$subject instanceof Group) {
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

        $groupRoles = $this->repository->findOneBy([
            'user' => $user,
            'group' => $subject
        ]);

        return (bool) $groupRoles;
    }
}
