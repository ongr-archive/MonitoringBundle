<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Unit\Metric;

use ONGR\MonitoringBundle\Metric\DocumentCount;

/**
 * Test for DocumentCount class.
 */
class DocumentCountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getValue method.
     */
    public function testGetValue()
    {
        $expected = 100;

        $docIteratorMock = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $docIteratorMock->expects($this->once())->method('getTotalCount')->willReturn(100);

        $manager = $this->getManagerMock();

        $repository = $this->getMockBuilder('ONGR\MonitoringBundle\ORM\Repository')
            ->setMethods(['createSearch', 'execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $search = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')
            ->disableOriginalConstructor()
            ->setMethods(['addQuery'])
            ->getMock();
        $search->expects($this->once())->method('addQuery');

        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        $repository->expects($this->once())->method('createSearch')
            ->will($this->returnValue($search));
        $repository->expects($this->once())->method('execute')
            ->will($this->returnValue($docIteratorMock));

        $metric = new DocumentCount($manager, 'docCount');
        $this->assertEquals($expected, $metric->getValue());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getManagerMock()
    {
        $manager = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        return $manager;
    }

    /**
     * Test getName method.
     */
    public function testGetName()
    {
        $expected = 'Metric name';
        $manager = $this->getManagerMock();

        $metric = new DocumentCount($manager, $expected);
        $this->assertEquals($expected, $metric->getName());
    }
}
