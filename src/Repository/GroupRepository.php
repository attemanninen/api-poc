<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupRole;
use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findByUserGroupRoles(User $user): array
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupRole::class, 'gr', Join::WITH, 'gr.group = g.id')
            ->where('gr.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }
}
