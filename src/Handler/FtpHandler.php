<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;

use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\FtpSyncBundle\Exception\MappingNotFoundException;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\FtpSyncBundle\Mapping\PropertyMappingFactory;
use Vich\FtpSyncBundle\Util\FtpStorage;

readonly class FtpHandler
{
    public function __construct(
        protected PropertyMappingFactory $factory,
        protected FtpStorage $storage
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function upload(object $obj, string $fieldName): void
    {
        $mapping = $this->getMapping($obj, $fieldName);

        // nothing to upload
        if (!$this->hasUploadedFile($obj, $mapping)) {
            return;
        }

        $this->storage->upload($obj, $mapping);
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function clean(object $obj, string $fieldName): void
    {
        $mapping = $this->getMapping($obj, $fieldName);

        // nothing uploaded, do not remove anything
        if (!$this->hasFile($obj, $mapping)) {
            return;
        }

        $this->remove($obj, $fieldName);
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function remove(object $obj, string $fieldName): void
    {
        $mapping = $this->getMapping($obj, $fieldName);
        $oldFilename = $mapping->getFile($obj)->getFilename();

        // nothing to remove, avoid dispatching useless events
        if (empty($oldFilename)) {
            return;
        }

        $this->storage->remove($obj, $mapping);
    }

    protected function hasFile(object $obj, PropertyMapping $mapping): bool
    {
        $file = $mapping->getFile($obj);

        return $file instanceof File;
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