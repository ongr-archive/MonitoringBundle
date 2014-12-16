<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Metric;

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\MonitoringBundle\Helper\MetricData;

/**
 * Class responsible for collecting metrics and save to ES.
 */
class CollectorService
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var MetricInterface[]
     */
    protected $metrics = [];

    /**
     * @param Manager $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * Collects metric data.
     *
     * @param string $metricName
     *
     * @throws \InvalidArgumentException
     */
    public function collect($metricName = null)
    {
        $metrics = $this->metrics;
        if ($metricName !== null && !isset($this->metrics[$metricName])) {
            throw new \InvalidArgumentException("Metric with name '{$metricName}' was not found.");
        } elseif ($metricName !== null && isset($this->metrics[$metricName])) {
            $metrics = [$this->metrics[$metricName]];
        }

        $this->collectMetrics($metrics);
        $this->manager->commit();
    }

    /**
     * Collects metric data and saves to ES.
     *
     * @param MetricInterface[] $metrics
     */
    protected function collectMetrics($metrics)
    {
        $repository = $this->manager->getRepository('ONGRMonitoringBundle:Metric');

        /** @var MetricInterface $metric */
        foreach ($metrics as $metric) {
            $values = $metric->getValue();

            $implodeKey = true;
            if (!is_array($values)) {
                $values = [$values];
                $implodeKey = false;
            }

            $name = $metric->getName();
            foreach ($values as $key => $value) {
                $tag = '';
                if ($value instanceof MetricData) {
                    $tag = $value->getTag();
                    $key = $value->getMetric();
                    $value = $value->getValue();
                }
                $data = [
                    'metric' => $implodeKey ? $name . $key : $name,
                    'value' => $value,
                    'tag' => $tag,
                    'collected' => new \DateTime('now', null),
                ];

                $document = $repository->createDocument();
                $document->assign($data);

                $this->manager->persist($document);
            }
        }
    }

    /**
     * @param MetricInterface $metric
     * @param string          $metricName
     *
     * @throws \InvalidArgumentException
     */
    public function addMetric($metric, $metricName)
    {
        if (!$metric instanceof MetricInterface) {
            throw new \InvalidArgumentException("Metric with name '{$metricName}' must implement MetricInterface.");
        }
        $this->metrics[$metricName] = $metric;
    }
}
