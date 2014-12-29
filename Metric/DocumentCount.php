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

use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
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
     * @var string
     */
    protected $repositoryClass;

    /**
     * @param Manager $manager
     * @param string  $name
     * @param string  $repositoryClass
     */
    public function __construct($manager, $name = null, $repositoryClass = null)
    {
        $this->manager = $manager;
        $this->name = $name;
        $this->repositoryClass = $repositoryClass;
    }

    /**
     * Get metric value.
     *
     * @return float
     */
    public function getValue()
    {
        $repository = $this->manager->getRepository($this->getRepositoryClass());
        if (!$repository) {
            $repository = $this->manager->getRepository('ONGRMonitoringBundle:Metric');
        }

        $search = $repository->createSearch()->addQuery(new MatchAllQuery());

        $results = $repository->execute($search, Repository::RESULTS_RAW_ITERATOR);

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

    /**
     * Get metric repository class.
     *
     * @return string
     */
    public function getRepositoryClass()
    {
        return $this->repositoryClass;
    }
}
