<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\Naming;

interface ConfigurableInterface
{
    public function configure(array $options): void;
}