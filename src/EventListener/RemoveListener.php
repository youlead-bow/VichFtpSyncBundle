<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class RemoveListener extends BaseListener
{
    private array $entities = [];

    /**
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if ($this->isFtpSyncable($object)) {
            // TODO A compléter
        }
    }

    public function postFlush(): void
    {
        foreach ($this->entities as $object) {
            foreach ($this->getFtpSyncableFields($object) as $field) {
                // TODO A compléter
            }
        }
        $this->entities = [];
    }
}