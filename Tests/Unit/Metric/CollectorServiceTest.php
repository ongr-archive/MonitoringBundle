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
     * @var string
     */
    protected $metricRepository = 'ONGRMonitoringBundle:Metric';

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
            ->method('getRepository')
            ->willReturn($repository);

        $manager->expects($this->exactly(2))->method('persist');

        $metrics = [];
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->any())->method('getRepositoryClass')->willReturn('ONGRMonitoringBundle:Metric');
        $metric1->expects($this->once())->method('getName');
        $metric1->expects($this->once())->method('getValue');
        $metrics['metric 1'] = $metric1;

        $metric2 = $this->getMetricMock();
        $metric1->expects($this->any())->method('getRepositoryClass')->willReturn('ONGRMonitoringBundle:Event');
        $metric2->expects($this->once())->method('getName');
        $metric2->expects($this->once())->method('getValue');
        $metrics['metric 2'] = $metric2;

        $service = new CollectorService($manager, $this->metricRepository, $metrics);

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
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');
        $manager->expects($this->exactly(2))->method('persist');

        /** @var \ONGR\MonitoringBundle\Metric\MetricInterface $metric1 */
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->once())->method('getName')->willReturn('DummyMetric');
        $metric1->expects($this->once())->method('getValue')->willReturn([1 => 'value-1', 2 => 'value-2']);

        $service = new CollectorService($manager, $this->metricRepository, ['metric 1' => $metric1]);

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

        /** @var Repository $repository */
        $repository = $this->getRepositoryMock();
        $repository->expects($this->exactly(2))->method('createDocument')->willReturn($metricMock);
        /** @var Manager $manager */
        $manager = $this->getManagerMock();
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');
        $manager->expects($this->exactly(2))->method('persist');

        /** @var \ONGR\MonitoringBundle\Metric\MetricInterface $metric1 */
        $metricData = new MetricData();
        $metricData->setTag('tag-1');
        $metricData->setValue('value-1');
        $metricData->setMetric('metric-1');

        $values[] = $metricData;
        $metricData = new MetricData();
        $metricData->setTag('tag-2');
        $metricData->setValue('value-2');
        $metricData->setMetric('metric-2');

        $values[] = $metricData;
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->once())->method('getName')->willReturn('DummyMetric');
        $metric1->expects($this->once())->method('getValue')->willReturn($values);

        $service = new CollectorService($manager, $this->metricRepository, ['metric 1' => $metric1]);

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

        $metric1 = new \stdClass();
        $service = new CollectorService($manager, $this->metricRepository, ['metric 1' => $metric1]);
    }

    /**
     * Tests non existing metric.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCollectException()
    {
        $manager = $this->getManagerMock();

        $metric1 = $this->getMetricMock();

        $service = new CollectorService($manager, $this->metricRepository, ['metric 1' => $metric1]);

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
            ->willReturn($repository);

        $manager->expects($this->once())->method('commit');

        $metrics = [];
        $metric1 = $this->getMetricMock();
        $metric1->expects($this->never())->method('getName');
        $metric1->expects($this->never())->method('getValue');
        $metrics['metric 1'] = $metric1;

        $metric2 = $this->getMetricMock();
        $metric2->expects($this->once())->method('getName');
        $metric2->expects($this->once())->method('getValue');
        $metrics['metric 2'] = $metric2;

        $metric3 = $this->getMetricMock();
        $metric3->expects($this->never())->method('getName');
        $metric3->expects($this->never())->method('getValue');
        $metrics['metric 3'] = $metric3;

        $metric4 = $this->getMetricMock();
        $metric4->expects($this->never())->method('getName');
        $metric4->expects($this->never())->method('getValue');
        $metrics['metric 4'] = $metric4;

        $service = new CollectorService($manager, $this->metricRepository, $metrics);

        $service->collect('metric 2');
    }
}
