<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class add metrics to metric collector service by tag and sets ES manager for monitoring bundle.
 */
class CollectorMetricsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $service = $container->getDefinition('ongr_monitoring.metric_collector');

        $taggedDefinitions = $container->findTaggedServiceIds('ongr_monitoring.metric');

        foreach ($taggedDefinitions as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['metric'])) {
                    throw new \RuntimeException(
                        "All services tagged with 'ongr_monitoring.metric' must have 'name' property set."
                    );
                }
                $service->addMethodCall('addMetric', [new Reference($id), $tag['metric']]);
            }
        }

        $manager = $container->getParameter('ongr_monitoring.es_manager');
        if ($manager) {
            $esManager = $container->findDefinition(sprintf('es.manager.%s', $manager));

            $container->setDefinition('ongr_monitoring.es_manager', $esManager);
        } else {
            throw new \RuntimeException(
                'ongr_monitoring.manager is not set.'
            );
        }
    }
}
