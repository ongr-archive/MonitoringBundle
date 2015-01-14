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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration.
 */
class ONGRMonitoringExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['es_manager'])) {
            $container->setParameter('ongr_monitoring.es_manager', $config['es_manager']);
        }

        $activeCollectors = [];
        if (!empty($config['metric_collectors'])) {
            $activeCollectors = $config['metric_collectors'];
        }
        $container->setParameter('ongr_monitoring.active_collectors', $activeCollectors);

        $metricCollector = $container->getDefinition('ongr_monitoring.metric_collector');
        $metricCollector->addArgument(new Reference($config['metric_collectors']['repository']));

        $taggedServices = $container->findTaggedServiceIds('kernel.event_listener');
        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall(
                'setRepository',
                [new Reference($config['commands']['repository'])]
            );
        }

        $trackedCommands = !empty($config['commands']['commands']) ? $config['commands']['commands'] : [];
        $container->setParameter('ongr_monitoring.tracked_commands', $trackedCommands);
    }
}
