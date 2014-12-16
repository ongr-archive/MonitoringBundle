<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\DependencyInjection\Compiler;

use ONGR\MonitoringBundle\DependencyInjection\Compiler\CollectorMetricsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Collector metric pass class test.
 */
class CollectorMetricsPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for process().
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('es.manager.monitoring', new Definition());
        $container->setParameter('ongr_monitoring.es_manager', 'monitoring');
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['metric' => 'metric']);
        $container->setDefinition('ongr_monitoring.fake.metric', $definition);

        $this->assertFalse(
            $container->getDefinition('ongr_monitoring.metric_collector')->hasMethodCall('addMetric')
        );

        $pass = new CollectorMetricsPass();
        $pass->process($container);

        $this->assertTrue(
            $container->getDefinition('ongr_monitoring.metric_collector')->hasMethodCall('addMetric')
        );
    }

    /**
     * Tests incorrect metric configuration.
     *
     * @expectedException \RuntimeException
     */
    public function testProcessException()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['BAD NAME' => 'metric']);
        $container->setDefinition('ongr_monitoring.fake.metric', $definition);

        $pass = new CollectorMetricsPass();
        $pass->process($container);
    }

    /**
     * Tests incorrect ES manager configuration.
     *
     * @expectedException \RuntimeException
     */
    public function testEsManagerNotSet()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('es.manager.monitoring', new Definition());
        $container->setParameter('ongr_monitoring.es_manager', '');
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());

        $pass = new CollectorMetricsPass();
        $pass->process($container);
    }
}
