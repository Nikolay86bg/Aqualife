<?php

/*
 * (c) 411 Marketing
 */

namespace App\Doctrine\Listener;

use App\Entity\AccountEvent;
use App\Service\AccountEventService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class AccountEventListener.
 */
class AccountEventListener implements EventSubscriber
{
    /**
     * @var AccountEventService
     */
    private $accountEventService;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * AccountEventListener constructor.
     *
     * @param TokenStorage        $tokenStorage
     * @param AccountEventService $accountEventService
     */
    public function __construct(TokenStorage $tokenStorage, AccountEventService $accountEventService)
    {
        $this->accountEventService = $accountEventService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var AccountEvent $entity */
        $entity = $args->getEntity();

        if (!($entity instanceof AccountEvent)) {
            return;
        }

        $user = null;
        if (($this->tokenStorage instanceof TokenStorage) && (null !== $this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        /** @var EntityManager $entityManager */
        $entityManager = $args->getEntityManager();

        /** @var UnitOfWork $unitOfWork */
        $unitOfWork = $entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $changes = $unitOfWork->getEntityChangeSet($entity);

        if ((array_key_exists('assignedTo', $changes)) ||
            ((array_key_exists('status', $changes)) && (AccountEvent::STATUS_IN_PROGRESS !== $entity->getStatus()))) {
            if (AccountEvent::STATUS_COMPLETED === $entity->getStatus() && $entity->getAssignedTo() === $user) {
                $entity->setClosedBy($user);
                $entity->setCompletedOnDate(new \DateTime());
            }

            if ($this->accountEventService->hasWorkStarted($entity)) {
                $this->accountEventService->stopWork($entity);

                $unitOfWork->recomputeSingleEntityChangeSet(
                    $entityManager->getClassMetadata(get_class($entity)),
                    $entity
                );
            }
        }
    }
}
