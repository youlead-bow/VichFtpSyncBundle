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
        $this->addMappingsSection($rootNode);

        return $treeBuilder;
    }


    private function addMappingsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('ftp_dsm')->isRequired()->end()
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
                ->end()
            ->end();
    }
}