<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Mapping;

interface PropertyMappingResolverInterface
{
    public function resolve(object|array $obj, string $fieldName, array $mappingData): PropertyMapping;
}