<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ongr_monitoring');
        $rootNode
            ->children()
                ->scalarNode('es_manager')
                    ->defaultValue('monitoring')
                ->end()
                ->append($this->getCommandsCollectorsNode())
                ->append($this->getMetricCollectorsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * Commands configuration node.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getCommandsCollectorsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('commands');

        /** NodeDefinition $node */
        $node
            ->info('Commands configuration node')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('repository')
                    ->defaultValue('default')
                ->end()
                ->arrayNode('commands')
                    ->prototype('variable')
                        ->treatNullLike([])
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * Metric collectors configuration node.
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function getMetricCollectorsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('metric_collector');
        /** NodeDefinition $node */
        $node
            ->info('Metric collectors configuration node')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('repository')
                    ->defaultValue('default')
                ->end()
                ->arrayNode('metrics')
                    ->prototype('array')
                        ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                            ->end()
                            ->scalarNode('document')
                                ->isRequired()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end();

        return $node;
    }
}
