<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\EventListener;

/**
 * Tests base event listener.
 */
class BaseEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if handle method works as expected.
     */
    public function testHandle()
    {
        $manager = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $listener = $this->getMockForAbstractClass('ONGR\MonitoringBundle\EventListener\BaseEventListener');
        $listener
            ->expects($this->once())
            ->method('capture');

        $listener->setManager($manager);
        $listener->handle($this->getCommandEventMock());
    }

    /**
     * Returns mocked ConsoleCommandEvent.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCommandEventMock()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Console\Event\ConsoleCommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
