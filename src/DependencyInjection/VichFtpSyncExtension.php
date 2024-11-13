<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class VichFtpSyncExtension extends Extension
{

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $config = $this->createNamerServices($container, $config);

        $container->setParameter('vich_ftp_sync.mappings', $config['mappings']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $this->registerListeners($container, $config);
    }


    protected function createNamerServices(ContainerBuilder $container, array $config): array
    {
        foreach ($config['mappings'] as $name => $mapping) {
            if (!empty($mapping['namer']['service'])) {
                $config['mappings'][$name] = $this->createNamerService($container, $name, $mapping);
            }
        }

        return $config;
    }

    protected function createNamerService(ContainerBuilder $container, string $mappingName, array $mapping): array
    {
        $serviceId = \sprintf('%s.%s', $mapping['namer']['service'], $mappingName);
        $container->setDefinition($serviceId, new ChildDefinition($mapping['namer']['service']));

        $mapping['namer']['service'] = $serviceId;

        return $mapping;
    }

    protected function registerListeners(ContainerBuilder $container, array $config): void
    {
        $servicesMap = [
            'delete_on_update' => ['name' => 'clean', 'priority' => 40, 'events' => ['preUpdate']],
            'delete_on_remove' => ['name' => 'remove', 'priority' => -10, 'events' => ['preRemove', 'postFlush']],
        ];

        foreach ($config['mappings'] as $name => $mapping) {
            // create optional listeners
            foreach ($servicesMap as $configOption => $service) {
                if (!$mapping[$configOption]) {
                    continue;
                }

                $this->createListener($container, $name, $service['name'], $service['events'], $service['priority']);
            }

            // the upload listener is mandatory
            $this->createListener($container, $name, 'upload', ['prePersist', 'preUpdate'], 10);
        }
    }

    protected function createListener(
        ContainerBuilder $container,
        string $name,
        string $type,
        array $events,
        int $priority = 0
    ): void {
        $definition = $container
            ->setDefinition(\sprintf('vich_ftp_sync.listener.%s.%s', $type, $name), new ChildDefinition(\sprintf('vich_ftp_sync.listener.%s.orm', $type)))
            ->replaceArgument(0, $name)
            ->replaceArgument(1, new Reference('vich_ftp_sync.adapter.orm'));

        foreach ($events as $event) {
            $definition->addTag('doctrine.event_listener', ['event' => $event, 'priority' => $priority]);
        }
    }
}