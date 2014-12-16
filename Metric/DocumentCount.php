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
use ONGR\ElasticsearchBundle\ORM\Repository;

/**
 * Counts documents on defined type.
 */
class DocumentCount implements MetricInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     * @param string  $name
     */
    public function __construct($manager, $name)
    {
        $this->manager = $manager;
        $this->name = $name;
    }

    /**
     * Get metric value.
     *
     * @return float
     */
    public function getValue()
    {
        $repository = $this->manager->getRepository('ONGRMonitoringBundle:Metric');

        $results = $repository->findBy(['metric' => $this->getName()], [], 0, null, Repository::RESULTS_RAW_ITERATOR);

        return $results->getTotalCount();
    }

    /**
     * Get metric name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
