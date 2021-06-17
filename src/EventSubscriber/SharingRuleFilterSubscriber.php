<?php

namespace App\EventSubscriber;

use App\Doctrine\ORM\Filter\SharingRuleFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

/**
 * Here we automatically enable SharingFilter on every request.
 *
 * This might not be ideal. The filter could also be enabled in the model's
 * manager...?
 */
class SharingRuleFilterSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    private Security $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->entityManager->getConfiguration()->addFilter(
            SharingRuleFilter::NAME,
            SharingRuleFilter::class
        );
        $filter = $this->entityManager->getFilters()->enable(SharingRuleFilter::NAME);
        $filter->setParameter('user_id', $this->security->getUser()->getId());
        $filter->setParameter('company_id', $this->security->getUser()->getCompany()->getId());
        $filter->setParameter('action', 'view');
    }
}
