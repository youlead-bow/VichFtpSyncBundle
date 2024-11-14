<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;


use Vich\FtpSyncBundle\Exception\MappingNotFoundException;

abstract class AbstractHandler
{
    public function __construct(
        protected readonly PropertyMappingFactory $factory
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