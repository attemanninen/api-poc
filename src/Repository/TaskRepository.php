<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function matchingWithTeam(Criteria $criteria, Team $team): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addCriteria($criteria)
            ->innerJoin('t.teams', 'tt')
            ->innerJoin('tt.team', 't')
            ->andWhere('t.id = :team')
            ->setParameter('team', $team);

        return $qb->getQuery()->execute();
    }

    public function matchingWithTeams(Criteria $criteria, iterable $teams): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addCriteria($criteria)
            ->innerJoin('t.teams', 'tt')
            ->innerJoin('tt.team', 't')
            ->andWhere('t.id IN (:teams)')
            ->setParameter('teams', $teams);

        return $qb->getQuery()->execute();
    }
}
