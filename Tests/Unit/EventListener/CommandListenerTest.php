<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Functional\EventListener;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\MonitoringBundle\Document\Event;
use ONGR\MonitoringBundle\EventListener\CommandListener;

/**
 * Class CommandListenerTest.
 */
class CommandListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests capture method behaviour.
     */
    public function testExecute()
    {
        $command = $this
            ->getMockBuilder('Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this
            ->getMockBuilder('Symfony\Component\Console\Event\ConsoleCommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())->method('getCommand')->will($this->returnValue($command));

        $eventManager = $this->getMock('ONGR\MonitoringBundle\Service\EventIdManager');
        $eventManager
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('bazId'));

        $eventParser = $this
            ->getMockBuilder('ONGR\MonitoringBundle\Helper\EventParser')
            ->disableOriginalConstructor()
            ->getMock();
        $eventParser
            ->expects($this->once())
            ->method('getDocument')
            ->willReturn(
                $this->getEventDocument(
                    [
                        '_id' => 'bazId',
                        'id' => 'bazId',
                        'command' => 'awesomeName',
                        'argument' => 'fooArg',
                        'started' => new \DateTime('2014-12-16', null),
                    ]
                )
            );

        $repository = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository
            ->expects($this->once())
            ->method('createDocument')
            ->will($this->returnValue(new Event()));

        $manager = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Event')
            ->will($this->returnValue($repository));

        $manager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->getEventDocument(
                    [
                        '_id' => 'bazId',
                        'id' => 'bazId',
                        'command' => 'awesomeName',
                        'argument' => 'fooArg',
                        'started' => new \DateTime('2014-12-16', null),
                    ]
                )
            );

        $listener = new CommandListener();
        $listener->setManager($manager);
        $listener->setEventParser($eventParser);
        $listener->setEventIdManager($eventManager);
        $listener->handle($event);
    }

    /**
     * Returns event model instance with provided data array.
     *
     * @param array $data
     *
     * @return DocumentInterface
     */
    protected function getEventDocument($data = [])
    {
        $document = new Event();
        $document->_id = $data['id'];
        $document->command = $data['command'];
        $document->argument = $data['argument'];
        $document->started = $data['started'];

        return $document;
    }
}
