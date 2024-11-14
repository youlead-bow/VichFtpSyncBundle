<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMappingDriversPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $drivers = [];
        $managers = [];
        if ($container->hasDefinition('doctrine_mongodb')) {
            $managers[] = new Reference('doctrine_mongodb');
        }
        if ($container->hasDefinition('doctrine')) {
            $managers[] = new Reference('doctrine');
        }
        if ($container->hasDefinition('doctrine_phpcr')) {
            $managers[] = new Reference('doctrine_phpcr');
        }

        if (count($managers) > 0) {
            $drivers[] = $container->getDefinition('vich_ftp_sync.metadata_driver.annotation')
                ->replaceArgument('$managerRegistryList', $managers);
        }

        $container
            ->getDefinition('vich_ftp_sync.metadata_driver.chain')
            ->replaceArgument(0, $drivers);
    }
}