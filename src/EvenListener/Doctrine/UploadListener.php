<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EvenListener\Doctrine;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class UploadListener extends BaseListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$this->isUploadable($object)) {
            return;
        }
        foreach ($this->getUploadableFields($object) as $field) {
            //$this->handler->upload($object, $field);
            // TODO A compléter
        }
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$this->isUploadable($object)) {
            return;
        }

        foreach ($this->getUploadableFields($object) as $field) {
            // TODO A compléter
            //$this->handler->upload($object, $field);
        }

        $this->adapter->recomputeChangeSet($event);
    }
}