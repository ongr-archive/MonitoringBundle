<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\Helper;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\MonitoringBundle\Document\Event;

/**
 * Class to test EventParser.
 */
class EventParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testGetDocument().
     *
     * @return array Data array for testGetDocument().
     */
    public function getTestGetDocumentData()
    {
        $data = [];

        // Case #0: Actual and expected commands/arguments should be the identical.
        $data[] = [
            [
                'command' => 'command1',
                'argument' => 'argument1 argument2',
            ],
            [
                'command' => 'command1',
                'argument' => 'argument1 argument2',
            ],
        ];

        return $data;
    }

    /**
     * Tests getDocument method.
     *
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $command
     * @param array                                                $expected
     *
     * @dataProvider getTestGetDocumentData
     */
    public function testGetDocument($command, $expected)
    {
        /** @var DocumentInterface $document */
        $document = new Event();

        /** @var \ONGR\MonitoringBundle\Helper\EventParser $eventParser */
        $eventParser = $this
            ->getMockBuilder('ONGR\MonitoringBundle\Helper\EventParser')
            ->setMethods(['getArgument', 'getCommandName'])
            ->getMock();
        $eventParser->expects($this->any())->method('getCommandName')->will($this->returnValue($command['command']));
        $eventParser->expects($this->any())->method('getArgument')->will($this->returnValue($command['argument']));

        /** @var \Symfony\Component\Console\Event\ConsoleCommandEvent $command */
        $command = new \stdClass();

        $document = $eventParser->getDocument($document, $command);

        $this->assertEquals($expected['command'], $document->getCommand());
        $this->assertEquals($expected['argument'], $document->getArgument());
    }
}
