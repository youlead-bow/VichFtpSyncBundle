<?php

declare(strict_types=1);


namespace Vich\FtpSyncBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('vich_ftp_sync');
        $rootNode = $treeBuilder->getRootNode();
        $this->addMetadataSection($rootNode);
        $this->addMappingsSection($rootNode);

        return $treeBuilder;
    }

    private function addMetadataSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('metadata')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('cache')->defaultValue('file')->end()
                            ->arrayNode('file_cache')
                            ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('dir')->defaultValue('%kernel.cache_dir%/vich_ftp_sync')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addMappingsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('uri_prefix')->isRequired()->end()
                            ->scalarNode('ftp_dsn')->isRequired()->end()
                            ->scalarNode('destination')->isRequired()->end()
                            ->arrayNode('namer')
                                ->addDefaultsIfNotSet()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(static fn ($v) => ['service' => $v, 'options' => []])
                                ->end()
                                ->children()
                                    ->scalarNode('service')->defaultNull()->end()
                                    ->variableNode('options')->defaultNull()->end()
                                ->end()
                            ->end()
                            ->arrayNode('directory_namer')
                                ->addDefaultsIfNotSet()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(static fn ($v) => ['service' => $v, 'options' => []])
                                ->end()
                                ->children()
                                    ->scalarNode('service')->defaultNull()->end()
                                    ->variableNode('options')->defaultNull()->end()
                                ->end()
                            ->end()
                            ->scalarNode('delete_on_remove')->defaultTrue()->end()
                            ->scalarNode('delete_on_update')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}