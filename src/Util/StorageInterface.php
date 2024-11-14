<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Util;

use Vich\FtpSyncBundle\Mapping\PropertyMapping;

interface StorageInterface
{
    public function upload(object $obj, PropertyMapping $mapping): void;

    public function remove(object $obj, PropertyMapping $mapping): ?bool;
}