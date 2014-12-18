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

/**
 * Metric interface for get name and value.
 */
interface MetricInterface
{
    /**
     * Get metric value.
     *
     * @return float
     */
    public function getValue();

    /**
     * Get metric name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get metric repository class.
     *
     * @return string
     */
    public function getRepositoryClass();
}
