<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TeamPermission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function matchingWithTeams(Criteria $criteria, iterable $teams): array
    {
        $qb = $this->createQueryBuilder('task')
            ->addCriteria($criteria)
            ->innerJoin('task.teams', 'taskTeam')
            ->innerJoin('taskTeam.team', 'team')
            ->andWhere('team.id IN (:teams)')
            ->setParameter('teams', $teams);

        return $qb->getQuery()->execute();
    }

    public function matchingWithAnyTeam(Criteria $criteria, User $user): array
    {
        $qb = $this->createQueryBuilder('task')
            ->addCriteria($criteria)
            ->innerJoin('task.teams', 'taskTeam')
            ->innerJoin('taskTeam.team', 'team')
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
