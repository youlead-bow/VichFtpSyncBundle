<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;


use League\Flysystem\Filesystem;
use Vich\FtpSyncBundle\Exception\MappingNotFoundException;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\FtpSyncBundle\Mapping\PropertyMappingFactory;

readonly class FtpHandler
{
    private Filesystem $ftp;

    public function __construct(
        protected PropertyMappingFactory $factory
    ) {
    }

    /**
     * @throws MappingNotFoundException
     */
    protected function getMapping(object|array $obj, string $fieldName, ?string $className = null): ?PropertyMapping
    {
        $mapping = $this->factory->fromField($obj, $fieldName, $className);

        if (null === $mapping) {
            throw new MappingNotFoundException(\sprintf('Mappage introuvable pour le champ "%s"', $fieldName));
        }

        return $mapping;
    }


}