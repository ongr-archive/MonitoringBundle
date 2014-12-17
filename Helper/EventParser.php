<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Helper;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Helper class to parse event data.
 */
class EventParser
{
    /**
     * Method to prepare document data for storage.
     *
     * @param DocumentInterface   $document Document object.
     * @param ConsoleCommandEvent $event    Event object.
     *
     * @return DocumentInterface Document object with filled data.
     */
    public function getDocument(DocumentInterface $document, $event)
    {
        $document->command = $this->getCommandName($event);
        $document->argument = $this->getArgument($event);
        $document->started = new \DateTime('now', null);

        return $document;
    }

    /**
     * Returns name of the command.
     *
     * @param ConsoleCommandEvent $event
     *
     * @return string Name of the command.
     */
    public function getCommandName($event)
    {
        return $event->getCommand()->getName();
    }

    /**
     * Returns argument of the command.
     *
     * @param ConsoleCommandEvent $event
     *
     * @return string Arguments passed to command.
     */
    public function getArgument($event)
    {
        return str_replace('\'' . $event->getCommand()->getName() . '\' ', '', $event->getInput());
    }
}
