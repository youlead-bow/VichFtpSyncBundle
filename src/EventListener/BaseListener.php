<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\EventListener;

use Vich\FtpSyncBundle\Handler\FtpHandler;
use Vich\FtpSyncBundle\Metadata\MetadataReader;
use Vich\UploaderBundle\Util\ClassUtils;

abstract class BaseListener
{
    public function __construct(
        protected readonly string $mapping,
        protected readonly MetadataReader $metadata,
        protected readonly FtpHandler $handler
    ) {
    }

    protected function isFtpSyncable(object $object): bool
    {
        return $this->metadata->isFtpSyncable(ClassUtils::getClass($object), $this->mapping);
    }

    protected function getFtpSyncableFields(object $object): array
    {
        $fields = $this->metadata->getFtpSyncableField(ClassUtils::getClass($object), $this->mapping);

        return \array_map(static fn (array $data): string => $data['propertyName'], $fields);
    }
}