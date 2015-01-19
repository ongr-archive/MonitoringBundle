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
    protected $repository;

    /**
     * @param Manager $manager
     * @param string  $name
     * @param string  $repository
     */
    public function __construct($manager, $name = null, $repository = null)
    {
        $this->manager = $manager;
        $this->name = $name;
        $this->repository = $repository;
    }

    /**
     * Get metric value.
     *
     * @return float
     */
    public function getValue()
    {
        /** @var Repository $repository */
        $repository = $this->repository;

        $search = $repository->createSearch();
        $search->addQuery(new MatchAllQuery());
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
}
