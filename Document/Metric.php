<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\AbstractDocument;

/**
 * Metric document.
 *
 * @ES\Document(type="metric")
 */
class Metric extends AbstractDocument
{
    /**
     * @var string
     *
     * @ES\Property(type="string", name="description")
     */
    public $metric;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="tag")
     */
    public $tag;

    /**
     * @var float
     *
     * @ES\Property(type="double", name="value")
     */
    public $value;

    /**
     * @var \DateTime
     *
     * @ES\Property(name="createdAt", type="date")
     */
    public $collected;

    /**
     * @return string
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * @param string $metric
     */
    public function setMetric($metric)
    {
        $this->metric = $metric;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return \DateTime
     */
    public function getCollected()
    {
        return $this->collected;
    }

    /**
     * @param \DateTime $collected
     */
    public function setCollected($collected)
    {
        $this->collected = $collected;
    }
}
