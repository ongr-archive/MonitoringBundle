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

        $trackedCommands = !empty($config['commands']) ? $config['commands'] : [];
        $container->setParameter('ongr_monitoring.tracked_commands', $trackedCommands);
    }
}
