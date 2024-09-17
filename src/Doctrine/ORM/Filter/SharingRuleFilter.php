<?php

namespace App\Doctrine\ORM\Filter;

use App\Entity\ShareableInterface;
use App\Entity\SharingRule;
use Doctrine\ORM\Mapping\ClassMetadata;

class SharingRuleFilter extends AbstractFilter
{
    public const NAME = 'sharing_rule';

    /**
     * {@inheritDoc}
     */
    public function addFilterConstraint(
        ClassMetadata $targetEntity,
        $targetTableAlias
    ) {
        if (!$targetEntity->reflClass->implementsInterface(ShareableInterface::class)) {
            return '';
        }

        // Get sharing rules to generate the condiditions
        $sharingRules = $this
            ->getEntityManager()
            ->getRepository(SharingRule::class)
            ->findByModelClassAndUserAndAction(
                $targetEntity->reflClass,
                $this->getRealParameter('user_id'),
                $this->getRealParameter('company_id'),
                $this->getRealParameter('action')
            );

        $conditions = [];
        foreach ($sharingRules as $rule) {
            $comparison = $rule->getEffect() === SharingRule::EFFECT_ALLOW ? '=' : '!=';

            $companyCondition = sprintf(
                '%s.company_id %s %s',
                $targetTableAlias,
                '=',
                $rule->getCompany()->getId()
            );

            $objectCondition = sprintf(
                '%s.%s %s %s',
                $targetTableAlias,
                $rule->getModelProperty(),
                $comparison,
                $rule->getModelValue()
            );

            $conditions[] = implode(' AND ', [
                $companyCondition,
                $objectCondition
            ]);
        }

        return implode(' OR ', $conditions);
    }
}
