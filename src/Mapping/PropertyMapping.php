<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Mapping;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Vich\FtpSyncBundle\Naming\DirectoryNamerInterface;
use Vich\FtpSyncBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\PropertyPathUtils;

class PropertyMapping
{
    private ?NamerInterface $namer = null;

    private ?DirectoryNamerInterface $directoryNamer = null;

    private ?array $mapping = null;

    private ?string $mappingName = null;

    private array $propertyPaths = [
        'file' => null,
        'name' => null
    ];

    private ?PropertyAccessor $accessor = null;

    public function __construct(string $filePropertyPath, string $fileNamePropertyPath, array $propertyPaths = [])
    {
        $this->propertyPaths = \array_merge(
            $this->propertyPaths,
            ['file' => $filePropertyPath, 'name' => $fileNamePropertyPath],
            $propertyPaths
        );
    }

    public function getFile(object $obj): ?File
    {
        return $this->readProperty($obj, 'file');
    }

    public function setFile(object $obj, File $file): void
    {
        $this->writeProperty($obj, 'file', $file);
    }

    public function getFileName(object|array $obj): ?string
    {
        return $this->readProperty($obj, 'name');
    }

    public function setFileName(object $obj, string $value): void
    {
        $this->writeProperty($obj, 'name', $value);
    }

    public function readProperty(object|array $obj, string $property): mixed
    {
        if (!\array_key_exists($property, $this->propertyPaths)) {
            throw new \InvalidArgumentException(\sprintf('Unknown property %s', $property));
        }

        if (!$this->propertyPaths[$property]) {
            // not configured
            return null;
        }

        $propertyPath = PropertyPathUtils::fixPropertyPath($obj, $this->propertyPaths[$property]);

        return $this->getAccessor()->getValue($obj, $propertyPath);
    }

    public function writeProperty(object $obj, string $property, mixed $value): void
    {
        if (!\array_key_exists($property, $this->propertyPaths)) {
            throw new \InvalidArgumentException(\sprintf('Propriété inconnue %s', $property));
        }

        if (!$this->propertyPaths[$property]) {
            // not configured
            return;
        }

        $propertyPath = PropertyPathUtils::fixPropertyPath($obj, $this->propertyPaths[$property]);
        $this->getAccessor()->setValue($obj, $propertyPath, $value);
    }

    public function getFilePropertyName(): string
    {
        return $this->propertyPaths['file'];
    }

    public function getNamer(): ?NamerInterface
    {
        return $this->namer;
    }

    public function setNamer(NamerInterface $namer): void
    {
        $this->namer = $namer;
    }

    public function hasNamer(): bool
    {
        return null !== $this->namer;
    }

    public function getDirectoryNamer(): ?DirectoryNamerInterface
    {
        return $this->directoryNamer;
    }

    public function setDirectoryNamer(DirectoryNamerInterface $directoryNamer): void
    {
        $this->directoryNamer = $directoryNamer;
    }

    public function hasDirectoryNamer(): bool
    {
        return null !== $this->directoryNamer;
    }

    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    public function getMappingName(): string
    {
        return $this->mappingName;
    }

    public function setMappingName($mappingName): void
    {
        $this->mappingName = $mappingName;
    }

    public function getUploadPath(object $obj): string
    {
        $path = [
            $this->getDestination(),
            $this->getUploadDir($obj),
            $this->getUploadName($obj)
        ];
        return implode('/', \array_filter($path));
    }

    public function getUploadName(object $obj): string
    {
        if (!$this->hasNamer()) {
            return $this->getFileName($obj);
        }

        return $this->getNamer()->name($obj, $this);
    }

    public function getUploadDir($obj): ?string
    {
        if (!$this->hasDirectoryNamer()) {
            return '';
        }

        $dir = $this->getDirectoryNamer()->directoryName($obj, $this);

        // strip the trailing directory separator if needed
        return $dir ? \rtrim($dir, '/\\') : $dir;
    }

    public function getDestination(): string
    {
        return $this->mapping['destination'];
    }

    public function getFtpDsn(): string
    {
        return $this->mapping['ftp_dsn'];
    }

    public function getUri($obj): string
    {
        $uri = [
            $this->getUriPrefix(),
            $this->getUploadDir($obj),
            $this->getUploadName($obj)
        ];
        return implode('/', \array_filter($uri));
    }

    public function getUriPrefix(): string
    {
        return $this->mapping['uri_prefix'];
    }

    private function getAccessor(): PropertyAccessor
    {
        if (null !== $this->accessor) {
            return $this->accessor;
        }

        return $this->accessor = PropertyAccess::createPropertyAccessor();
    }
}