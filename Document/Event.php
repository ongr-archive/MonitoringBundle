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
 * Event document.
 *
 * @ES\Document(type="event")
 */
class Event implements DocumentInterface
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="command")
     */
    public $command;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="argument")
     */
    public $argument;

    /**
     * @var \DateTime
     *
     * @ES\Property(name="started", type="date")
     */
    public $started;

    /**
     * @var \DateTime
     *
     * @ES\Property(name="ended", type="date")
     */
    public $ended;

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
