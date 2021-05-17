<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\TeamPermission;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findByUserTeamPermissions(User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin(TeamPermission::class, 'tp', Join::WITH, 'tp.team = t.id')
            ->where('tp.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }
}
