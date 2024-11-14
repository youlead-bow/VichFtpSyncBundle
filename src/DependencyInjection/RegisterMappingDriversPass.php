<?php

declare(strict_types=1);

namespace Vich\FtpSyncBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterMappingDriversPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {

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
            $drivers[] = $container->getDefinition('vich_uploader.metadata_driver.annotation')
                ->replaceArgument('$managerRegistryList', $managers);
        }

        $container
            ->getDefinition('vich_uploader.metadata_driver.chain')
            ->replaceArgument(0, $drivers);
    }
}