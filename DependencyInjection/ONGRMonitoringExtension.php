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

        $container->setParameter('ongr_monitoring.es_manager', $config['es_manager']);

        $this->configureCollector($container, $config);
        $this->configureCommands($container, $config);
    }

    /**
     * Configure metric collector.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureCollector(ContainerBuilder $container, $config)
    {
        $metrics = [];
        if (!empty($config['metric_collector']) && !empty($config['metric_collector']['metrics'])) {
            $metrics = $config['metric_collector']['metrics'];
        }
        $container->setParameter('ongr_monitoring.active_metrics', $metrics);

        if ($config['metric_collector']['repository'] == 'default') {
            $metricRepository = sprintf(
                'es.manager.%s.%s',
                strtolower($config['es_manager']),
                'metric'
            );
        } else {
            $metricRepository = $config['metric_collector']['repository'];
        }

        $container->setParameter('ongr_monitoring.metric_collector.repository', $metricRepository);

        $metricCollector = $container->getDefinition('ongr_monitoring.metric_collector');
        $metricCollector->addArgument(new Reference($metricRepository));
    }

    /**
     * Register listeners and sets command events repository.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureCommands(ContainerBuilder $container, $config)
    {
        if ($config['commands']['repository'] == 'default') {
            $eventRepository = sprintf(
                'es.manager.%s.%s',
                strtolower($config['es_manager']),
                'event'
            );
        } else {
            $eventRepository = $config['commands']['repository'];
        }

        $taggedServices = $container->findTaggedServiceIds('kernel.event_listener');
        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->addMethodCall(
                'setRepository',
                [new Reference($eventRepository)]
            );
        }
        $container->setParameter('ongr_monitoring.commands.repository', $eventRepository);

        $trackedCommands = !empty($config['commands']['commands']) ? $config['commands']['commands'] : [];
        $container->setParameter('ongr_monitoring.tracked_commands', $trackedCommands);
    }
}
