<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Naming;

use Vich\FtpSyncBundle\Mapping\PropertyMapping;

interface NamerInterface
{
    public function name(object $object, PropertyMapping $mapping): string;
}