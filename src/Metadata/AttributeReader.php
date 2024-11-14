<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Metadata;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Vich\FtpSyncBundle\Mapping\AnnotationInterface;

final class AttributeReader
{
    public function getClassAnnotations(ReflectionClass $class): array
    {
        return $this->convertToAttributeInstances($class->getAttributes());
    }

    public function getClassAnnotation(ReflectionClass $class, string $annotationName): ?AnnotationInterface
    {
        return $this->getClassAnnotations($class)[$annotationName] ?? null;
    }

    public function getMethodAnnotations(ReflectionMethod $method): array
    {
        return $this->convertToAttributeInstances($method->getAttributes());
    }

    public function getMethodAnnotation(ReflectionMethod $method, string $annotationName): ?AnnotationInterface
    {
        return $this->getMethodAnnotations($method)[$annotationName] ?? null;
    }

    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        return $this->convertToAttributeInstances($property->getAttributes());
    }

    public function getPropertyAnnotation(ReflectionProperty $property, string $annotationName): ?AnnotationInterface
    {
        return $this->getPropertyAnnotations($property)[$annotationName] ?? null;
    }

     private function convertToAttributeInstances(array $attributes): array
    {
        $instances = [];

        foreach ($attributes as $attribute) {
            $attributeName = $attribute->getName();
            $instance = $attribute->newInstance();

            if (!$instance instanceof AnnotationInterface) {
                continue;
            }

            $instances[$attributeName] = $instance;
        }

        return $instances;
    }
}