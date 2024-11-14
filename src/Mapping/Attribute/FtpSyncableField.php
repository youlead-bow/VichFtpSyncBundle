<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Mapping\Attribute;

use Vich\FtpSyncBundle\Mapping\AnnotationInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class FtpSyncableField implements AnnotationInterface
{

    public function __construct(
        private string $mapping
    )
    {
    }

    public function getMapping(): string
    {
        return $this->mapping;
    }


}