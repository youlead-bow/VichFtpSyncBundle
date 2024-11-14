<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Mapping;


use Doctrine\Persistence\Proxy;
use Vich\FtpSyncBundle\Exception\NotFtpSyncableException;
use Vich\FtpSyncBundle\Metadata\MetadataReader;
use Vich\UploaderBundle\Util\ClassUtils;

final class PropertyMappingFactory
{
    public function __construct(
        private readonly MetadataReader $metadata,
        private readonly PropertyMappingResolverInterface $resolver,
    ) {
    }

    public function fromObject(object|array $obj, ?string $className = null, ?string $mappingName = null): array
    {
        if ($obj instanceof Proxy) {
            $obj->__load();
        }

        $class = $this->getClassName($obj, $className);
        $this->checkFtpSyncable($class);

        $mappings = [];
        foreach ($this->metadata->getFtpSyncableFields($class) as $field => $mappingData) {
            if (null !== $mappingName && $mappingName !== $mappingData['mapping']) {
                continue;
            }

            $mappings[] = $this->resolver->resolve($obj, $field, $mappingData);
        }

        return $mappings;
    }

    public function fromField(object|array $obj, string $field, ?string $className = null): ?PropertyMapping
    {
        if ($obj instanceof Proxy) {
            $obj->__load();
        }

        $class = $this->getClassName($obj, $className);
        $this->checkFtpSyncable($class);

        $mappingData = $this->metadata->getFtpSyncableField($class, $field);
        if (null === $mappingData) {
            return null;
        }

        return $this->resolver->resolve($obj, $field, $mappingData);
    }

    public function fromFirstField(object|array $obj, ?string $className = null): ?PropertyMapping
    {
        if ($obj instanceof Proxy) {
            $obj->__load();
        }

        $class = $this->getClassName($obj, $className);
        $this->checkFtpSyncable($class);

        $mappingData = $this->metadata->getFtpSyncableFields($class);
        if (0 === \count($mappingData)) {
            return null;
        }

        return $this->resolver->resolve($obj, \key($mappingData), \reset($mappingData));
    }

    private function checkFtpSyncable(string $class): void
    {
        if (!$this->metadata->isFtpSyncable($class)) {
            throw new NotFtpSyncableException(\sprintf('La classe "%s" n\'est pas synchronisable via un ftp. Vous avez probablement simplement oublié d\'ajouter `#[Vfs\FtpSyncable]` au-dessus de votre entité. Vider le cache peut également résoudre le problème.', $class));
        }
    }

    /**
     * Returns the className of the given object.
     *
     * @param object|mixed $object    The object to inspect
     * @param string|null  $className User specified className
     *
     * @throws \RuntimeException
     */
    private function getClassName(mixed $object, ?string $className = null): string
    {
        if (null !== $className) {
            return $className;
        }

        if (\is_object($object)) {
            return ClassUtils::getClass($object);
        }

        throw new \RuntimeException('Impossible to determine the class name. Either specify it explicitly or give an object');
    }
}