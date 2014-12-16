<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Service;

/**
 * Helper class to parse event data.
 */
class EventIdManager
{
    /**
     * Method to get id of the event.
     *
     * @param object $event Instance of ConsoleEvent object.
     *
     * @return string Id of the Event.
     */
    public function getId($event)
    {
        return md5(spl_object_hash($event));
    }
}
