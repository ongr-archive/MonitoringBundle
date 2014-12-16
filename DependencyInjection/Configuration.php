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
                ->arrayNode('commands')
                    ->prototype('variable')
                        ->treatNullLike([])
                    ->end()
                ->end()
                ->arrayNode('metric_collectors')
                    ->prototype('variable')
                        ->treatNullLike([])
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
