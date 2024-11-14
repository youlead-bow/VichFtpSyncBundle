<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;

readonly class UrlHandler extends AbstractHandler
{
    public function resolve(object $obj, string $fieldName): string
    {
        $mapping = $this->getMapping($obj, $fieldName);

        return $mapping->getUri($obj);
    }
}