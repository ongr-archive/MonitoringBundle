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
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * Metric document.
 *
 * @ES\Document(type="metric")
 */
class Metric implements DocumentInterface
{
    use DocumentTrait;

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
     * Assign data into document.
     *
     * @param array $data
     */
    public function assign($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
