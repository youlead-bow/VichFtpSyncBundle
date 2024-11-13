<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Metadata;

use Metadata\ClassMetadata as BaseClassMetadata;

final class ClassMetadata extends BaseClassMetadata
{
    public array $fields = [];

    public function serialize(): string
    {
        return \serialize([$this->fields, serialize(parent::serializeToArray())]);
    }

    public function unserialize($str): void
    {
        [$this->fields, $parentStr] = \unserialize($str);

        parent::unserializeFromArray(unserialize($parentStr));
    }
}