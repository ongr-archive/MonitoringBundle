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

use ONGR\MonitoringBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Provides bunlde configuration data.
     *
     * @return array
     */
    public function getTestConfigurationData()
    {
        $expectedConfiguration = [
            'es_manager' => 'monitoring',
            'commands' => [],
        ];

        $out = [];

        // Case #0 Set manager.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
            ],
            array_merge($expectedConfiguration, ['es_manager' => 'foo_manager']),
        ];

        // Case #0 Set metric collectors.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
                'metric_collectors' => [
                    'document_count' => [
                        [
                            'name' => 'name',
                            'document' => 'ONGRMonitoringBundle:Event',
                        ],
                    ],
                ]
            ],
            array_merge(
                $expectedConfiguration,
                [
                    'es_manager' => 'foo_manager',
                    'metric_collectors' => [
                        'document_count' => [
                            [
                                'name' => 'name',
                                'document' => 'ONGRMonitoringBundle:Event',
                            ],
                        ],
                    ],
                ]
            ),
        ];

        // Case #2 Invalid document name.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
                'metric_collectors' => [
                    'document_count' => [
                        [
                            'name' => 'name',
                            'document' => 'ONGRMonitoringundle:Event',
                        ],
                    ],
                ]
            ],
            $expectedConfiguration,
            true,
            'Invalid configuration for path "ongr_monitoring.metric_collectors.document_count.0.document": "ONGRMonitoringundle:Event" is not a ES document.',
        ];

        return $out;
    }

    /**
     * Tests if expected default values are added.
     *
     * @param array  $config
     * @param array  $expected
     * @param bool   $exception
     * @param string $exceptionMessage
     *
     * @dataProvider getTestConfigurationData
     */
    public function testConfiguration($config, $expected, $exception = false, $exceptionMessage = '')
    {
        if ($exception) {
            $this->setExpectedException(
                '\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                $exceptionMessage
            );
        }

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$config]);
        $this->assertEquals($expected, $processedConfig);
    }
}
