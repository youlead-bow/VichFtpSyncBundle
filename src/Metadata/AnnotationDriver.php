<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Metadata;


use Metadata\ClassMetadata as JMSClassMetadata;
use Metadata\Driver\AdvancedDriverInterface;
use Vich\FtpSyncBundle\Mapping\Attribute\FtpSyncable;
use Vich\FtpSyncBundle\Mapping\Attribute\FtpSyncableField;

readonly class AnnotationDriver implements AdvancedDriverInterface
{
    public function __construct(
        protected AttributeReader $reader,
        private array $managerRegistryList
    ) {
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?JMSClassMetadata
    {
        if (!$this->isFtpSyncable($class)) {
            return null;
        }

        $classMetadata = new ClassMetadata($class->name);
        $classMetadata->fileResources[] = $class->getFileName();

        $classes = [];
        do {
            $classes[] = $class;
            $class = $class->getParentClass();
        } while (false !== $class);
        $classes = \array_reverse($classes);
        $properties = [];
        foreach ($classes as $cls) {
            $properties = [...$properties, ...$cls->getProperties()];
        }

        foreach ($properties as $property) {
            $ftpSyncableField = $this->reader->getPropertyAnnotation($property, FtpSyncableField::class);
            if (null === $ftpSyncableField) {
                continue;
            }
            /* @var $ftpSyncableField FtpSyncableField */

            $fieldMetadata = [
                'mapping' => $ftpSyncableField->getMapping()
            ];

            $classMetadata->fields[$property->getName()] = $fieldMetadata;
        }

        return $classMetadata;
    }

    /**
     * @throws \ReflectionException
     */
    public function getAllClassNames(): array
    {
        $classes = [];
        $metadata = [];

        foreach ($this->managerRegistryList as $managerRegisty) {
            $managers = $managerRegisty->getManagers();
            foreach ($managers as $manager) {
                $metadata[] = $manager->getMetadataFactory()->getAllMetadata();
            }
        }

        $metadata = \array_merge(...$metadata);

        /** @var \Doctrine\Persistence\Mapping\ClassMetadata $classMeta */
        foreach ($metadata as $classMeta) {
            if ($this->isFtpSyncable(new \ReflectionClass($classMeta->getName()))) {
                $classes[] = $classMeta->getName();
            }
        }

        return $classes;
    }

    protected function isFtpSyncable(\ReflectionClass $class): bool
    {
        return null !== $this->reader->getClassAnnotation($class, FtpSyncable::class);
    }
}