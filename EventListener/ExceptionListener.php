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

use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\MonitoringBundle\Document\Event;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * Class to listen to console commands exception events.
 */
class ExceptionListener extends BaseEventListener
{
    /**
     * {@inheritdoc}
     *
     * @param ConsoleCommandEvent $event ConsoleCommandEvent object.
     */
    protected function capture($event)
    {
        /** @var Repository $repository */
        $repository = $this->manager->getRepository($this->getRepository());

        /** @var Event $document */
        $document = $repository->createDocument();
        $document = $this->eventParser->getDocument($document, $event);

        $document->setId($this->eventIdManager->getId($event->getCommand()));
        $document->setStatus(Event::EVENT_EXCEPTION);

        $this->manager->persist($document);
        $this->manager->commit();
    }
}
