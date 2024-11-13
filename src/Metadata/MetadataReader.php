<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Metadata;


use Metadata\AdvancedMetadataFactoryInterface;

final readonly class MetadataReader
{
    public function __construct(private AdvancedMetadataFactoryInterface $reader)
    {
    }

    /**
     * Tells if the given class is FtpSyncable.
     *
     * @param string      $class   The class name to test (FQCN)
     * @param string|null $mapping If given, also checks that the object has the given mapping
     *
     * @throws MappingNotFoundException
     */
    public function isFtpSyncable(string $class, ?string $mapping = null): bool
    {
        $metadata = $this->reader->getMetadataForClass($class);

        if (null === $metadata) {
            return false;
        }

        if (null === $mapping) {
            return true;
        }

        foreach ($this->getFtpSyncableFields($class) as $fieldMetadata) {
            if ($fieldMetadata['mapping'] === $mapping) {
                return true;
            }
        }

        return false;
    }

    public function getFtpSyncableClasses(): ?array
    {
        return $this->reader->getAllClassNames();
    }

    public function getFtpSyncableFields(string $class, ?string $mapping = null): array
    {
        if (null === $metadata = $this->reader->getMetadataForClass($class)) {
            throw MappingNotFoundException::createNotFoundForClass($mapping ?? '', $class);
        }
        $FtpSyncableFields = [];

        /** @var ClassMetadata $classMetadata */
        foreach ($metadata->classMetadata as $classMetadata) {
            $FtpSyncableFields = \array_merge($FtpSyncableFields, $classMetadata->fields);
        }

        if (null !== $mapping) {
            $FtpSyncableFields = \array_filter($FtpSyncableFields, static fn (array $fieldMetadata): bool => $fieldMetadata['mapping'] === $mapping);
        }

        return $FtpSyncableFields;
    }

    public function getFtpSyncableField(string $class, string $field): mixed
    {
        $fieldsMetadata = $this->getFtpSyncableFields($class);

        return $fieldsMetadata[$field] ?? null;
    }
}