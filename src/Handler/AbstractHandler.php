<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;


use Vich\FtpSyncBundle\Exception\MappingNotFoundException;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\FtpSyncBundle\Mapping\PropertyMappingFactory;
use Vich\FtpSyncBundle\Util\FtpStorage;

abstract readonly class AbstractHandler
{
    public function __construct(
        protected PropertyMappingFactory $factory,
        protected FtpStorage $storage
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