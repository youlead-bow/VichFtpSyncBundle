<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use League\Flysystem\FilesystemException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class UploadListener extends BaseListener
{
    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$this->isFtpSyncable($object)) {
            return;
        }
        foreach ($this->getFtpSyncableFields($object) as $field) {
            $this->handler->upload($object, $field);
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$this->isFtpSyncable($object)) {
            return;
        }

        foreach ($this->getFtpSyncableFields($object) as $field) {
            $this->handler->upload($object, $field);
        }
    }
}