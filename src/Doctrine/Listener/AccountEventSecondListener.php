<?php

/*
 * (c) 411 Marketing
 */

namespace App\Doctrine\Listener;

use App\Entity\AccountEvent;
use App\Entity\AccountEventChange;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Saves Account Event Changes to DB
 * Class AccountEventSecondListener.
 */
class AccountEventSecondListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * AccountEventSecondListener constructor.
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param OnFlushEventArgs $args
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityUpdates()
        );

        $user = null;
        if (($this->tokenStorage instanceof TokenStorage) && (null !== $this->tokenStorage->getToken())) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        foreach ($entities as $entity) {
            if (!($entity instanceof AccountEvent)) {
                continue;
            }

            $changes = $uow->getEntityChangeSet($entity);

            if (array_key_exists('event', $changes)) {
                $accountEventChange = new AccountEventChange();
                $accountEventChange->setAccountEvent($entity);
                $accountEventChange->setOldEvent($changes['event'][0]);
                $accountEventChange->setNewEvent($changes['event'][1]);
                if ($user) {
                    $accountEventChange->setCreatedBy($user);
                }
                $em->persist($accountEventChange);

                $md = $em->getClassMetadata(get_class($accountEventChange));
                $uow->computeChangeSet($md, $accountEventChange);
            }
        }
    }
}
