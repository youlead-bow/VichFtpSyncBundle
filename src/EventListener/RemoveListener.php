<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Proxy;
use League\Flysystem\FilesystemException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

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
            if ($object instanceof Proxy) {
                $object->__load();
            }
            $this->entities[] = clone $object;
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function postFlush(): void
    {
        foreach ($this->entities as $object) {
            foreach ($this->getFtpSyncableFields($object) as $field) {
                $this->handler->remove($object, $field);
            }
        }
        $this->entities = [];
    }
}