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
     * Provides bundle configuration data.
     *
     * @return array
     */
    public function getTestConfigurationData()
    {
        $expectedConfiguration = [
            'es_manager' => 'monitoring',
        ];

        $out = [];

        // Case #0 Set manager.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
            ],
            array_merge(
                $expectedConfiguration,
                [
                    'es_manager' => 'foo_manager',
                    'metric_collector' => [
                        'repository' => 'default',
                        'metrics' => [],
                    ],
                    'commands' => [
                        'repository' => 'default',
                        'commands' => [],
                    ],
                ]
            ),
        ];

        // Case #1 Set metric collectors.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
                'metric_collector' => [
                    'repository' => 'default',
                    'metrics' => [
                        'document_count' => [
                            [
                                'name' => 'name',
                                'document' => 'es.manager.monitoring.metric',
                            ],
                        ],
                    ],
                ],
            ],
            array_merge(
                $expectedConfiguration,
                [
                    'es_manager' => 'foo_manager',
                    'metric_collector' => [
                        'repository' => 'default',
                        'metrics' => [
                            'document_count' => [
                                [
                                    'name' => 'name',
                                    'document' => 'es.manager.monitoring.metric',
                                ],
                            ],
                        ],
                    ],
                    'commands' => [
                        'repository' => 'default',
                        'commands' => [],
                    ],
                ]
            ),
        ];

        // Case #2 Set commands.
        $out[] = [
            [
                'es_manager' => 'foo_manager',
                'commands' => [
                    'repository' => 'es.manager.monitoring.event',
                    'commands' => [
                        'fooCommand',
                    ],
                ],
            ],
            array_merge(
                $expectedConfiguration,
                [
                    'es_manager' => 'foo_manager',
                    'commands' => [
                        'repository' => 'es.manager.monitoring.event',
                        'commands' => [
                            'fooCommand',
                        ],
                    ],
                    'metric_collector' => [
                        'repository' => 'default',
                        'metrics' => [],
                    ],
                ]
            ),
        ];

        return $out;
    }

    /**
     * Tests if expected default values are added.
     *
     * @param array $config
     * @param array $expected
     *
     * @dataProvider getTestConfigurationData
     */
    public function testConfiguration($config, $expected)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$config]);
        $this->assertEquals($expected, $processedConfig);
    }
}
