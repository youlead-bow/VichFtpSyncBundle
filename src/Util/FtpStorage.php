<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Util;


use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\FileAbstraction\ReplacingFile;

class FtpStorage
{
    private Filesystem $ftp;

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function upload(object $obj, PropertyMapping $mapping): void
    {
        $this->getFTP($mapping);
        $file = $mapping->getFile($obj);
        if (!$file instanceof UploadedFile && !$file instanceof ReplacingFile) {
            throw new \LogicException('No uploadable file found');
        }

        $remotePath = $mapping->getUploadPath($obj);
        $content = $file->getContent();
        $this->ftp->write($remotePath, $content);
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function remove(object $obj, PropertyMapping $mapping): ?bool
    {
        $this->getFTP($mapping);
        $name = $mapping->getFile($obj)->getFilename();

        if (empty($name)) {
            return false;
        }

        $remotePath = $mapping->getUploadPath($obj);
        if ($this->ftp->has($remotePath)) {
            $this->ftp->delete($remotePath);
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function getFTP(PropertyMapping $mapping): void
    {
        $passport = FtpPasseport::getInstance($mapping->getFtpDsn());

        $adapter = new FtpAdapter(FtpConnectionOptions::fromArray([
            'host' => $passport->getUri(),
            'username' => $passport->getUser(),
            'password' => $passport->getPass(),
            'port' => $passport->getPort(),
            'timeout' => 30,
        ]));
        $this->ftp = new Filesystem($adapter);
    }
}