<?php

namespace App\Repository;

use App\Entity\Group;
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

    public function matchingWithGroup(Criteria $criteria, Group $group): array
    {
        $qb = $this->createQueryBuilder('t')
            ->addCriteria($criteria)
            ->innerJoin('t.groups', 'g')
            ->andWhere('g.group = :group')
            ->setParameter('group', $group);

        return $qb->getQuery()->execute();
    }
}
