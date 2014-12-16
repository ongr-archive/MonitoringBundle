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
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Class to listen to console commands terminate events.
 */
class TerminateListener extends BaseEventListener
{
    /**
     * {@inheritdoc}
     *
     * @param ConsoleTerminateEvent $event ConsoleTerminateEvent object.
     */
    protected function capture($event)
    {
        $ended = new \DateTime('now', null);
        /** @var Repository $repository */
        $document = $this->manager->getRepository('ONGRMonitoringBundle:Event')->find(
            $this->eventIdManager->getId($event->getCommand())
        );

        $document->ended = $ended;

        $this->manager->persist($document);
        $this->manager->commit();
    }
}
