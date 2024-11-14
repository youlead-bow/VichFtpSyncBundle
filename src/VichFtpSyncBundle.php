<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vich\FtpSyncBundle\DependencyInjection\RegisterMappingDriversPass;

class VichFtpSyncBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterMappingDriversPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}