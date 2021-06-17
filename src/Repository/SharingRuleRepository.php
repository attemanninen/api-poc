<?php

namespace App\Repository;

use App\Entity\SharingRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SharingRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharingRule::class);
    }

    public function findByModelClassAndUserAndAction(
        string $modelClass,
        $userId,
        $companyId,
        ?string $action = 'view'
    ): iterable {
        // TODO: Add team condition!
        // Also, it might be possible to get the right sharing rules with
        // user id only, but I am not that good with SQL.
        $dql = "
            SELECT sr
            FROM App\Entity\SharingRule sr
            WHERE sr.permissions LIKE :action
                AND (
                    (sr.subjectType = 'company' AND sr.subjectId = :company_id)
                    OR (sr.subjectType = 'user' AND sr.subjectId = :user_id)
                )
        ";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters([
            'action' => '%' . $action . '%',
            'company_id' => $companyId,
            'user_id' => $userId
        ]);

        return $query->getResult();
    }
}
