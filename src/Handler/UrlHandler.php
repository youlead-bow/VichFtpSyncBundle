<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;

use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\FtpSyncBundle\Exception\MappingNotFoundException;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\FtpSyncBundle\Mapping\PropertyMappingFactory;
use Vich\FtpSyncBundle\Util\FtpStorage;

readonly class UrlHandler extends AbstractHandler
{
    public function resolve(object $obj, string $fieldName): string
    {
        $mapping = $this->getMapping($obj, $fieldName);

        return $mapping->getUri($obj);
    }
}