<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Functional\DependencyInjection;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class ServiceCreationTest extends ElasticsearchTestCase
{
    /**
     * Test ES manager service.
     */
    public function testEsManagerIsSet()
    {
        $container = self::createClient()->getContainer();

        $this->assertTrue($container->has('ongr_monitoring.es_manager'));
        $this->assertInstanceOf(
            'ONGR\ElasticsearchBundle\ORM\Manager',
            $container->get('ongr_monitoring.es_manager')
        );
    }
}
