<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Metadata;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class CacheWarmer implements CacheWarmerInterface
{
    public function __construct(private readonly string $dir, private readonly MetadataReader $metadataReader)
    {
    }

    public function warmUp(string $cacheDir, string $buildDir = null): array
    {
        if (empty($this->dir)) {
            return [];
        }
        $files = [];
        if (!\is_dir($this->dir)) {
            if (!\mkdir($concurrentDirectory = $this->dir, 0o777, true) && !\is_dir($concurrentDirectory)) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        $ftpSyncableClasses = $this->metadataReader->getFtpSyncableClasses();
        foreach ($ftpSyncableClasses as $class) {
            $this->metadataReader->getFtpSyncableFields($class);
            $files[] = $class;
        }

        // TODO it could be nice if we return $files, to allow to exploit preloading...
        return [];
    }

    public function isOptional(): bool
    {
        return true;
    }
}