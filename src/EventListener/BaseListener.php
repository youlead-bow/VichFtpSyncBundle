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

    /**
     * Checks if the given object is uploadable using the current mapping.
     *
     * @param object $object The object to test
     */
    protected function isFtpSyncable(object $object): bool
    {
        return $this->metadata->isFtpSyncable(ClassUtils::getClass($object), $this->mapping);
    }

    /**
     * Returns a list of uploadable fields for the given object and mapping.
     *
     * @param object $object The object to use
     *
     * @return array|string[] A list of field names
     *
     * @throws \Vich\UploaderBundle\Exception\MappingNotFoundException
     */
    protected function getFtpSyncableFields(object $object): array
    {
        $fields = $this->metadata->getFtpSyncableFields(ClassUtils::getClass($object), $this->mapping);

        return \array_map(static fn (array $data): string => $data['propertyName'], $fields);
    }
}