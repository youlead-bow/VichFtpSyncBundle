<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Mapping\Attribute;

use Vich\FtpSyncBundle\Mapping\AnnotationInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class FtpSyncable implements AnnotationInterface
{

}