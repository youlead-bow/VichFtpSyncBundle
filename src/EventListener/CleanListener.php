<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class CleanListener extends BaseListener
{
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();

        if (!$this->isFtpSyncable($object)) {
            return;
        }

        foreach ($this->getFtpSyncableFields($object) as $field) {
            // TODO A compléter
        }
    }
}