<?php

/*
 * (c) 411 Marketing
 */

namespace App\Doctrine\Listener;

use App\Doctrine\Traits\TimestampableEntityTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Class TimestampableEntityListener.
 */
class TimestampableEntityListener implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var TimestampableEntityTrait $entity */
        $entity = $args->getEntity();

        if (in_array(TimestampableEntityTrait::class, class_uses($entity), true)) {
            $date = new \DateTime();

            $entity->setCreatedAt($date);
            $entity->setUpdatedAt($date);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        /** @var TimestampableEntityTrait $entity */
        $entity = $args->getEntity();

        if (in_array(TimestampableEntityTrait::class, class_uses($entity), true)) {
            $entity->setUpdatedAt(new \DateTime());

            $entityManager = $args->getEntityManager();
            $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
                $entityManager->getClassMetadata(get_class($entity)),
                $entity
            );
        }
    }
}
