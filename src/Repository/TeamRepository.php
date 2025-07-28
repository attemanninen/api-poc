<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\TeamPermission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findByUserTeamPermissions(User $user): array
    {
        $qb = $this->createQueryBuilder('team')
            ->innerJoin(
                TeamPermission::class,
                'teamPermission',
                Join::WITH,
                'teamPermission.team = team.id'
            )
            ->where('teamPermission.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }

    /**
     * This is not a good name...
     */
    public function matchingWithUserTeamPermissions(
        Criteria $criteria,
        User $user,
    ): array {
        $qb = $this->createQueryBuilder('team')
            ->addCriteria($criteria)
            ->innerJoin(
                TeamPermission::class,
                'teamPermission',
                Join::WITH,
                'teamPermission.team = team.id'
            )
            ->where('teamPermission.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }
}
