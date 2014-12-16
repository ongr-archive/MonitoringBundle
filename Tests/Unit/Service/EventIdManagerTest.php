<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Functional\Service;

use ONGR\MonitoringBundle\Service\EventIdManager;

/**
 * Class to test EventIdManager.
 */
class EventIdManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Method to test getId() method.
     */
    public function testGetId()
    {
        // Initiating EventIdManager object.
        $eventIdManager = new EventIdManager();

        // Dummy object.
        $dummyObject = new \stdClass();

        // Getting id of the object.
        $objId = $eventIdManager->getId($dummyObject);

        $this->assertInternalType('string', $objId);
        $this->assertTrue(strlen($objId) == 32);
    }
}
