<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\EventListener;

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\MonitoringBundle\Helper\EventParser;
use ONGR\MonitoringBundle\Service\EventIdManager;
use Symfony\Component\Console\Event\ConsoleEvent;

/**
 * Base class for event listeners.
 */
abstract class BaseEventListener
{
    /**
     * @var Manager Es manager.
     */
    public $manager;

    /**
     * @var Repository
     */
    public $repository;

    /**
     * @var EventParser Event parser object.
     */
    public $eventParser;

    /**
     * @var EventIdManager Event id manager object.
     */
    public $eventIdManager;

    /**
     * Setter for the EventParser object.
     *
     * @param EventParser $eventParser EventParser object.
     */
    public function setEventParser(EventParser $eventParser)
    {
        $this->eventParser = $eventParser;
    }

    /**
     * Setter for the EventIdManager object.
     *
     * @param EventIdManager $eventIdManager EventIdManager object.
     */
    public function setEventIdManager(EventIdManager $eventIdManager)
    {
        $this->eventIdManager = $eventIdManager;
    }

    /**
     * Checks if manager exists before firing event.
     *
     * @param ConsoleEvent $event Instance of ConsoleEvent.
     */
    public function handle($event)
    {
        if ($this->getManager() !== null) {
            $this->capture($event);
        }
    }

    /**
     * This method is executed when event is fired.
     *
     * @param ConsoleEvent $event Instance of ConsoleEvent.
     */
    abstract protected function capture($event);

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Method to set ES Manager.
     *
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
}
