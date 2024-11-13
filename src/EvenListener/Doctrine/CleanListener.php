<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EvenListener\Doctrine;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class CleanListener extends BaseListener
{
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();

        if (!$this->isUploadable($object)) {
            return;
        }

        foreach ($this->getUploadableFields($object) as $field) {
            //$this->handler->clean($object, $field);
            // TODO A compléter
        }

        $this->adapter->recomputeChangeSet($event);
    }
}