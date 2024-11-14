<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Util;


use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\FtpSyncBundle\Mapping\PropertyMapping;

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
        if (!$file instanceof File) {
            throw new \LogicException('Aucun fichier trouvé');
        }

        $remotePath = $mapping->getUploadPath($obj);
        $stream = fopen($file->getRealPath(), 'r+');
        $this->ftp->writeStream($remotePath, $stream, ['visibility' => 'public']);
    }

    /**
     * @throws ExceptionInterface
     * @throws FilesystemException
     */
    public function remove(object $obj, PropertyMapping $mapping): ?bool
    {
        $this->getFTP($mapping);
        $oldFilename = $mapping->getFileName($obj);

        if (empty($oldFilename)) {
            return false;
        }

        $remotePath = $mapping->getUploadPath($obj);
        if ($this->ftp->has($remotePath)) {
            $this->ftp->delete($remotePath);
        }

        return true;
    }

    /**
     * @throws ExceptionInterface
     */
    private function getFTP(PropertyMapping $mapping): void
    {
        $passport = FtpPasseport::getInstance($mapping->getFtpDsn());

        $adapter = new FtpAdapter(FtpConnectionOptions::fromArray([
            'host' => $passport->getHost(),
            'username' => $passport->getUser(),
            'password' => $passport->getPass(),
            'port' => $passport->getPort(),
            'timeout' => 30,
        ]));
        $this->ftp = new Filesystem($adapter);
    }
}