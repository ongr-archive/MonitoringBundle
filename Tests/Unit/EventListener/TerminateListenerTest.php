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

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\MonitoringBundle\Document\Event;
use ONGR\MonitoringBundle\EventListener\TerminateListener;

class TerminateListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests capture method behaviour.
     */
    public function testExecute()
    {
        $event = $this
            ->getMockBuilder('Symfony\Component\Console\Event\ConsoleCommandEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue('testCommandObject'));


        $eventParser = $this->getMock('ONGR\MonitoringBundle\Helper\EventParser');

        $eventManager = $this->getMock('ONGR\MonitoringBundle\Service\EventIdManager');

        $eventManager
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('bazId'));

        $repository = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository
            ->expects($this->once())
            ->method('find')
            ->willReturn(
                $this->getEventModel(
                    [
                        '_id' => 'bazId',
                        'ended' => new \DateTime('2014-12-14', null),
                    ]
                )
            );

        $manager = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Event')
            ->willReturn($repository);

        $manager
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->getEventModel(
                    [
                        '_id' => 'bazId',
                        'ended' => new \DateTime('now'),
                    ]
                )
            );

        $listener = new TerminateListener();
        $listener->setManager($manager);
        $listener->setEventParser($eventParser);
        $listener->setEventIdManager($eventManager);
        $listener->handle($event);
    }

    /**
     * Returns event document instance with provided data array.
     *
     * @param array $data
     *
     * @return DocumentInterface
     */
    protected function getEventModel($data = [])
    {
        $document = new Event();
        $document->_id = $data['_id'];
        $document->ended = $data['ended'];

        return $document;
    }
}
