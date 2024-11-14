<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\Util;


use League\Flysystem\Filesystem;
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
     */
    public function upload(object $obj, PropertyMapping $mapping): void
    {
        $this->getFTP($mapping);
        $file = $mapping->getFile($obj);
        if (!$file instanceof UploadedFile && !$file instanceof ReplacingFile) {
            throw new \LogicException('No uploadable file found');
        }

        $name = $mapping->getUploadName($obj);
        $dir = $mapping->getUploadDir($obj);

        //TODO: faire upload ftp
    }

    /**
     * @throws ExceptionInterface
     */
    public function remove(object $obj, PropertyMapping $mapping): ?bool
    {
        $this->getFTP($mapping);
        $name = $mapping->getFile($obj)->getFilename();

        if (empty($name)) {
            return false;
        }

        //TODO: faire remove ftp
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