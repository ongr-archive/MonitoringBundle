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
use Symfony\Component\DependencyInjection\Definition;
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
        $manager = $container->getParameter('ongr_monitoring.es_manager');
        if ($manager) {
            $esManager = $container->findDefinition(sprintf('es.manager.%s', $manager));

            $managerDefinition = $container->setDefinition('ongr_monitoring.es_manager', $esManager);
        } else {
            throw new \RuntimeException(
                'ongr_monitoring.manager is not set.'
            );
        }

        $service = $container->getDefinition('ongr_monitoring.metric_collector');

        $taggedDefinitions = $container->findTaggedServiceIds('ongr_monitoring.metric');
        $metrics = $container->getParameter('ongr_monitoring.active_collectors');
        $collectors = [];
        foreach ($taggedDefinitions as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['metric'])) {
                    throw new \RuntimeException(
                        "All services tagged with 'ongr_monitoring.metric' must have 'name' property set."
                    );
                }
                if (isset($metrics[$tag['metric']])) {
                    foreach ($metrics[$tag['metric']] as $metric) {
                        $this->checkIfDocumentMapped($metric['document'], $container);
                        $collectors[$metric['name']] = new Definition(
                            new Reference($id),
                            [$managerDefinition, $metric['name'], $metric['document']]
                        );
                    }
                }
            }
        }
        $service->addArgument($collectors);
    }

    /**
     * Checks if document has mapping data in ES metadata collector.
     *
     * @param string           $documentClass
     * @param ContainerBuilder $container
     *
     * @throws \RuntimeException
     */
    private function checkIfDocumentMapped($documentClass, ContainerBuilder $container)
    {
        $metadataCollector = $container->get('es.metadata_collector');

        if (!$metadataCollector) {
            throw new \RuntimeException(
                'Could not load es.metadata collector. ElasticsearchBundle not enabled?'
            );
        }

        if (!$metadataCollector->getMappingByNamespace($documentClass)) {
            throw new \RuntimeException(
                "Invalid ES document mapping for class: {$documentClass}"
            );
        }
    }
}
