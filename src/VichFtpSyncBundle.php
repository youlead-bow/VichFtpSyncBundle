<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Vich\FtpSyncBundle\DependencyInjection\RegisterMappingDriversPass;

class VichFtpSyncBundle extends AbstractBundle
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterMappingDriversPass());
    }
}