<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Handler;

use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\FtpSyncBundle\Metadata\AnnotationDriver;

readonly class FtpHandler extends AnnotationDriver
{
    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function upload(object $obj, string $fieldName): void
    {
        $mapping = $this->getMapping($obj, $fieldName);

        // nothing to upload
        if (!$this->hasFile($obj, $mapping)) {
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
}