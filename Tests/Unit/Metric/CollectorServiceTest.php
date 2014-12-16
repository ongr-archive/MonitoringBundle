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

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\MonitoringBundle\Document\Metric;
use ONGR\MonitoringBundle\Helper\MetricData;
use ONGR\MonitoringBundle\Metric\CollectorService;

/**
 * Class test CollectorService class.
 */
class CollectorServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Method to test collecting of metrics.
     */
    public function testCollect()
    {
        $repository = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository
            ->expects($this->exactly(2))
            ->method('createDocument')
            ->will($this->returnValue(new Metric()));

        $manager = $this->getManagerMock();
        $manager->expects($this->once())->method('commit');
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Metric')
            ->willReturn($repository);

        $manager->expects($this->exactly(2))->method('persist');

        $metric1 = $this->getMetricMock();
        $metric1->expects($this->once())->method('getName');
        $metric1->expects($this->once())->method('getValue');

        $metric2 = $this->getMetricMock();
        $metric2->expects($this->once())->method('getName');
        $metric2->expects($this->once())->method('getValue');

        $service = new CollectorService($manager);
        $service->addMetric($metric1, 'metric 1');
        $service->addMetric($metric2, 'metric 2');

        $service->collect();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getManagerMock()
    {
        $manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->setMethods(['flush', 'createDocument', 'persist', 'commit', 'getRepository'])
            ->disableOriginalConstructor()
            ->getMock();

        return $manager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMetricMock()
    {
        $metric = $this->getMock('ONGR\MonitoringBundle\Metric\MetricInterface');

        return $metric;
    }

    /**
     * Test to check whether two metric objects are created when metric collector returns array.
     */
    public function testCollectArray()
    {
        /** @var Metric $metricMock */
        $metricMock = $this
            ->getMockBuilder('ONGR\MonitoringBundle\Document\Metric')
            ->disableOriginalConstructor()
            ->getMock();
        $metricMock->expects($this->exactly(2))->method('assign')->withConsecutive(
            [$this->contains('value-1')],
            [$this->contains('value-2')]
        );

        /** Repository $repository */
        $repository = $this->getRepositoryMock();
        $repository
            ->expects($this->exactly(2))
            ->method('createDocument')
            ->will($this->returnValue($metricMock));

        /** @var Manager $manager */
        $manager = $this->getManagerMock();

        $manager->expects($this->once())->method('commit');
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Metric')
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');
        $manager->expects($this->exactly(2))->method('persist');

        /** @var \ONGR\MonitoringBundle\Metric\MetricInterface $metric1 */
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->once())->method('getName')->willReturn('DummyMetric');
        $metric1->expects($this->once())->method('getValue')->willReturn([1 => 'value-1', 2 => 'value-2']);

        $service = new CollectorService($manager);
        $service->addMetric($metric1, 'metric 1');

        $service->collect();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRepositoryMock()
    {
        $repository = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        return $repository;
    }

    /**
     * Test to check whether two metric objects are created when metric collector returns array of MetricData.
     */
    public function testCollectMetricDataArray()
    {
        /** @var Metric $metricMock */
        $metricMock = $this->getMock('ONGR\MonitoringBundle\Document\Metric');
        $metricMock->expects($this->exactly(2))->method('assign')->withConsecutive(
            [$this->contains('tag-1')],
            [$this->contains('value-2')]
        );

        /** @var Repository $repository */
        $repository = $this->getRepositoryMock();
        $repository->expects($this->exactly(2))->method('createDocument')->willReturn($metricMock);
        /** @var Manager $manager */
        $manager = $this->getManagerMock();
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Metric')
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');
        $manager->expects($this->exactly(2))->method('persist');

        /** @var \ONGR\MonitoringBundle\Metric\MetricInterface $metric1 */
        $metricData = new MetricData();
        $metricData->setTag('tag-1');
        $metricData->setValue('value-1');
        $values[] = $metricData;
        $metricData = new MetricData();
        $metricData->setTag('tag-2');
        $metricData->setValue('value-2');
        $values[] = $metricData;
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->once())->method('getName')->willReturn('DummyMetric');
        $metric1->expects($this->once())->method('getValue')->willReturn($values);

        $service = new CollectorService($manager);
        $service->addMetric($metric1, 'metric 1');

        $service->collect();
    }

    /**
     * Tests if unregistered metric is not executed.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddMetricException()
    {
        $manager = $this->getManagerMock();
        $service = new CollectorService($manager);

        $metric1 = new \stdClass();
        $service->addMetric($metric1, 'metric 1');
    }

    /**
     * Tests non existing metric.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCollectException()
    {
        $manager = $this->getManagerMock();
        $service = new CollectorService($manager);

        $metric1 = $this->getMetricMock();
        $service->addMetric($metric1, 'metric 1');

        $service->collect('not existing metric');
    }

    /**
     * Method to test collecting of specific metrics.
     */
    public function testCollectSpecificMetric()
    {
        /** @var Repository $repository */
        $repository = $this->getRepositoryMock();
        $repository->expects($this->any())->method('createDocument')->willReturn(new Metric());
        /** @var Manager $manager */
        $manager = $this->getManagerMock();
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->with('ONGRMonitoringBundle:Metric')
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');

        $metric1 = $this->getMetricMock();
        $metric1->expects($this->never())->method('getName');
        $metric1->expects($this->never())->method('getValue');

        $metric2 = $this->getMetricMock();
        $metric2->expects($this->once())->method('getName');
        $metric2->expects($this->once())->method('getValue');

        $metric3 = $this->getMetricMock();
        $metric3->expects($this->never())->method('getName');
        $metric3->expects($this->never())->method('getValue');

        $metric4 = $this->getMetricMock();
        $metric4->expects($this->never())->method('getName');
        $metric4->expects($this->never())->method('getValue');

        $service = new CollectorService($manager);
        $service->addMetric($metric1, 'metric 1');
        $service->addMetric($metric2, 'metric 2');
        $service->addMetric($metric3, 'metric 3');
        $service->addMetric($metric4, 'metric 4');

        $service->collect('metric 2');
    }
}
