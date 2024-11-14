<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
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
            // TODO A compléter
        }
    }

    /**
     * @throws ExceptionInterface
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