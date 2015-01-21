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
        $container = $this->getContainerMock();

        $container->setDefinition('es.metadata_collector', new Definition());
        $container->setDefinition('es.manager.monitoring', new Definition());
        $container->setParameter('ongr_monitoring.es_manager', 'monitoring');
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());
        $container->setDefinition('es.manager.monitoring.metric', new Definition());

        $container->setParameter(
            'ongr_monitoring.active_metrics',
            [
                'document_count' => [
                    [
                        'name' => 'foo',
                        'document' => 'es.manager.monitoring.metric',
                    ],
                ],
            ]
        );

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['BAD NAME' => 'document_count']);
        $container->setDefinition('ongr_monitoring.document_count.metric', $definition);

        $this->assertFalse(
            $container->getDefinition('ongr_monitoring.metric_collector')->hasMethodCall('addMetric')
        );

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['metric' => 'document_count']);
        $container->setDefinition('ongr_monitoring.document_count.metric', $definition);

        $pass = new CollectorMetricsPass();
        $pass->process($container);

        $this->assertArrayHasKey('foo', $container->getDefinition('ongr_monitoring.metric_collector')->getArgument(0));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerMock()
    {
        $container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(['get'])
            ->getMock();

        return $container;
    }

    /**
     * Tests incorrect metric configuration.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage All services tagged with 'ongr_monitoring.metric' must have 'name' property set.
     */
    public function testProcessException()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());
        $container->setDefinition('es.manager.monitoring', new Definition());

        $container->setParameter('ongr_monitoring.es_manager', 'monitoring');
        $container->setParameter(
            'ongr_monitoring.active_metrics',
            [
                'document_count' => [
                    [
                        'name' => 'foo',
                        'document' => 'AcmeTestBundle:Foo',
                    ],
                ],
            ]
        );

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['BAD NAME' => 'metric ']);
        $container->setDefinition('ongr_monitoring.fake.metric', $definition);

        $pass = new CollectorMetricsPass();
        $pass->process($container);
    }

    /**
     * Tests when repository does not exists in ES.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Repository service does not exists: es.manager.monitoring.foo
     */
    public function testRepositoryNotFoundException()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('ongr_monitoring.metric_collector', new Definition());
        $container->setDefinition('es.manager.monitoring', new Definition());
        $container->setParameter('ongr_monitoring.es_manager', 'monitoring');

        $container->setParameter(
            'ongr_monitoring.active_metrics',
            [
                'document_count' => [
                    [
                        'name' => 'foo',
                        'document' => 'es.manager.monitoring.foo',
                    ],
                ],
            ]
        );

        $definition = new Definition();
        $definition->addTag('ongr_monitoring.metric', ['metric' => 'document_count']);
        $container->setDefinition('ongr_monitoring.document_count.metric', $definition);

        $pass = new CollectorMetricsPass();
        $pass->process($container);
    }

    /**
     * Tests incorrect ES manager configuration.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage ongr_monitoring.manager is not set.
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
