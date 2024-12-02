<?php

namespace App\Event\Listener;

use DateTime;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
class EntityPrePersistListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        $entity->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
    }
}
