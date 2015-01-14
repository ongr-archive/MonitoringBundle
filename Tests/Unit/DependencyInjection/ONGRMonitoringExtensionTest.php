<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\DependencyInjection;

use ONGR\MonitoringBundle\DependencyInjection\ONGRMonitoringExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class to test ONGRMonitoringExtension.
 */
class ONGRMonitoringExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getTestParamsData()
    {
        $customConfig = [
            'ongr_monitoring' => [
                'es_manager' => 'monitoring',
            ],
        ];

        return [
            [
                [],
                'ongr_monitoring.es_manager',
                'monitoring',
            ],
            [
                $customConfig,
                'ongr_monitoring.es_manager',
                'monitoring',
            ],
            [
                [
                    'ongr_monitoring' => [
                        'metric_collectors' => [
                            'document_count' => [
                                [
                                    'name' => 'foo',
                                    'document' => 'AcmeTestBundle:Foo',
                                ],
                            ],
                        ],
                    ],
                ],
                'ongr_monitoring.active_collectors',
                [
                    'repository' => 'es.manager.monitoring.metric',
                    'document_count' => [
                        [
                            'name' => 'foo',
                            'document' => 'AcmeTestBundle:Foo',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Data provider for testBuild().
     *
     * @return array
     */
    public function getTestBuildData()
    {
        return [
            ['ongr_monitoring.command_listener.class', 'ONGR\MonitoringBundle\EventListener\CommandListener'],
            ['ongr_monitoring.terminate_listener.class', 'ONGR\MonitoringBundle\EventListener\TerminateListener'],
            ['ongr_monitoring.event_parser.class', 'ONGR\MonitoringBundle\Helper\EventParser'],
            ['ongr_monitoring.event_id_manager.class', 'ONGR\MonitoringBundle\Service\EventIdManager'],
        ];
    }

    /**
     * Tests if right parameters are set.
     *
     * @param array  $config
     * @param string $param
     * @param mixed  $expected
     *
     * @dataProvider getTestParamsData
     */
    public function testParams($config, $param, $expected)
    {
        $container = $this->getContainer();

        $extension = new ONGRMonitoringExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasParameter($param), 'Expected parameter does not exist.');
        $this->assertEquals($expected, $container->getParameter($param), 'Parameter has been set with wrong value.');
    }

    /**
     * Setup container needed for testing.
     *
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }
}
