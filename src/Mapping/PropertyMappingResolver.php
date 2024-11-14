<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Mapping;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Vich\FtpSyncBundle\Exception\MappingNotFoundException;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\ClassUtils;

final readonly class PropertyMappingResolver implements PropertyMappingResolverInterface
{
    public function __construct(
        private ContainerInterface $container,
        private array $mappings
    ) {
    }

    public function resolve(object|array $obj, string $fieldName, array $mappingData): PropertyMapping
    {
        if (!\array_key_exists($mappingData['mapping'], $this->mappings)) {
            $className = \is_object($obj) ? ClassUtils::getClass($obj) : '[array]';
            throw MappingNotFoundException::createNotFoundForClassAndField($mappingData['mapping'], $className, $fieldName);
        }

        $config = $this->mappings[$mappingData['mapping']];

        $mapping = new PropertyMapping($fieldName, $mappingData['fileNameProperty'], $mappingData);
        $mapping->setMappingName($mappingData['mapping']);
        $mapping->setMapping($config);

        if (!empty($config['namer']) && null !== $config['namer']['service']) {
            $namerConfig = $config['namer'];
            $namer = $this->container->get($namerConfig['service']);

            if (!empty($namerConfig['options'])) {
                if (!$namer instanceof NamerInterface) {
                    throw new \LogicException(\sprintf('Namer %s can not receive options as it does not implement ConfigurableInterface.', $namerConfig['service']));
                }
                $namer->configure($namerConfig['options']);
            }

            $mapping->setNamer($namer);
        }

        if (!empty($config['directory_namer']) && null !== $config['directory_namer']['service']) {
            $namerConfig = $config['directory_namer'];
            $namer = $this->container->get($namerConfig['service']);

            if (!empty($namerConfig['options'])) {
                if (!$namer instanceof DirectoryNamerInterface) {
                    throw new \LogicException(\sprintf('Namer %s can not receive options as it does not implement ConfigurableInterface.', $namerConfig['service']));
                }
                $namer->configure($namerConfig['options']);
            }

            $mapping->setDirectoryNamer($namer);
        }

        return $mapping;
    }
}