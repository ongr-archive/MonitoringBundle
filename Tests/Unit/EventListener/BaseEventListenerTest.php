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
        $listener->setTrackedCommands(['foo']);
        $listener->handle($this->getCommandEventMock('foo'));
    }

    /**
     * Tests if handle method not fired for registered command.
     */
    public function testUnregisteredCommand()
    {
        $manager = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $listener = $this->getMockForAbstractClass('ONGR\MonitoringBundle\EventListener\BaseEventListener');
        $listener
            ->expects($this->never())
            ->method('capture');

        $listener->setManager($manager);
        $listener->setTrackedCommands(['bar']);
        $listener->handle($this->getCommandEventMock('foo'));
    }

    /**
     * Returns mocked ConsoleCommandEvent.
     *
     * @param string $commandName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCommandEventMock($commandName)
    {
        $commandMock = $this
            ->getMockBuilder('Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();
        $commandMock->expects($this->once())->method('getName')->willReturn($commandName);

        $commandEventMock = $this
            ->getMockBuilder('Symfony\Component\Console\Event\ConsoleCommandEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getCommand'])
            ->getMock();
        $commandEventMock->expects($this->once())->method('getCommand')->will($this->returnValue($commandMock));

        return $commandEventMock;
    }
}
