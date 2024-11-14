<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Naming;

use Vich\FtpSyncBundle\Mapping\PropertyMapping;

interface DirectoryNamerInterface
{
    public function directoryName(object|array $object, PropertyMapping $mapping): string;
}