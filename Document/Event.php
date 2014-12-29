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
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * @param string $argument
     */
    public function setArgument($argument)
    {
        $this->argument = $argument;
    }

    /**
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param \DateTime $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return \DateTime
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * @param \DateTime $ended
     */
    public function setEnded($ended)
    {
        $this->ended = $ended;
    }
}
