<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\Command;

use ONGR\MonitoringBundle\Command\CollectMetricCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectMetricCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests command execution.
     */
    public function testExecute()
    {
        $service = $this
            ->getMockBuilder('ONGR\MonitoringBundle\Metric\CollectorService')
            ->disableOriginalConstructor()
            ->getMock();

        $service
            ->expects($this->once())
            ->method('collect')
            ->with('foo');

        $container = new ContainerBuilder();
        $container->set('ongr_monitoring.metric_collector', $service);

        $command = new CollectMetricCommand();
        $command->setContainer($container);

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $command->run(new ArrayInput(['--metric' => 'foo']), $output);
    }
}
