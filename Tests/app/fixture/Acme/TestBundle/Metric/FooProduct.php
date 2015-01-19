<?php

/*
 * This file is part of the ONGR package.
 *
 * Copyright (c) 2014-2015 NFQ Technologies UAB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ONGR\MonitoringBundle\Tests\app\fixture\Acme\TestBundle\Metric;

use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\MonitoringBundle\Metric\MetricInterface;

/**
 * Custom metric collector.
 */
class FooProduct implements MetricInterface
{
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
        $repository = $this->repository;

        /** @var Search $search */
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
        return 'foo_product';
    }
}