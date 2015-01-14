<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Functional\EventListener;

use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\DSL\Query\TermQuery;
use ONGR\ElasticsearchBundle\DSL\Query\TermsQuery;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\MonitoringBundle\Command\CollectMetricCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Tests if command listeners works as expected.
 */
class CommandListenerTest extends ElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'foo',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'bar',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'pizza',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test if two commands from separate applications are logged correctly.
     */
    public function testCapture()
    {
        $container = $this->getContainer();

        $command = new CollectMetricCommand();
        $command->setContainer($container);

        $app = new Application();
        $dispatcher = $container->get('event_dispatcher');
        $app->setDispatcher($dispatcher);
        $app->setAutoExit(false);
        $app->add($command);

        $applicationTester = new ApplicationTester($app);
        $applicationTester->run(
            [
                'command' => $command->getName(),
                '--metric' => 'foo',
            ]
        );

        $this->assertEquals(1, $this->getDocumentCount('ONGRMonitoringBundle:Event'), 'Should be created 1 event log');
        $this->assertEquals(
            1,
            $this->getDocumentCount('ONGRMonitoringBundle:Metric'),
            'Should be created 1 metric log'
        );
        $this->assertEquals(
            3,
            $this->getDocumentCount('AcmeTestBundle:Product'),
            'Should be created 3 products'
        );

        $command1 = new CollectMetricCommand();
        $command1->setContainer($container);

        $app1 = new Application();
        $app1->setDispatcher($dispatcher);
        $app1->setAutoExit(false);
        $app1->add($command1);

        $applicationTester = new ApplicationTester($app1);
        $applicationTester->run(
            [
                'command' => $command1->getName(),
                '--metric' => 'bar',
            ]
        );

        $this->assertEquals(2, $this->getDocumentCount('ONGRMonitoringBundle:Event'), 'Should be created 2 event logs');
        $this->assertEquals(
            2,
            $this->getDocumentCount('ONGRMonitoringBundle:Metric'),
            'Should be created 2 metric logs'
        );
    }

    /**
     * Test if exception was logged correctly.
     */
    public function testExceptionListener()
    {
        $container = $this->getContainer();

        $command = new CollectMetricCommand();
        $command->setContainer($container);

        $app = new Application();
        $dispatcher = $container->get('event_dispatcher');
        $app->setDispatcher($dispatcher);
        $app->setAutoExit(false);
        $app->add($command);

        $applicationTester = new ApplicationTester($app);
        $applicationTester->run(
            [
                'command' => $command->getName(),
                '--metric' => 'buz',
            ]
        );

        $eventDocument = $this->getEventByStatus('ONGRMonitoringBundle:Event', 'exception');

        $this->assertEquals(
            1,
            $eventDocument['hits']['total'],
            'Should be created 1 command log wiht status exception.'
        );
        $this->assertEquals(
            "Metric with name 'buz' was not found.",
            $eventDocument['hits']['hits'][0]['_source']['message'],
            "Message text should be set to Metric with name 'buz' was not found."
        );
    }

    /**
     * Get document count by status from ES.
     *
     * @param string $documentType
     * @param string $status
     *
     * @return mixed
     */
    private function getEventByStatus($documentType, $status)
    {
        $manager = $this->getManager('monitoring', false);
        $repository = $manager->getRepository($documentType);
        $search = $repository->createSearch()->addQuery(new TermQuery('status', $status, []));

        return $repository->execute($search, Repository::RESULTS_RAW);
    }

    /**
     * Get document count from ES.
     *
     * @param string $documentType
     *
     * @return mixed
     */
    private function getDocumentCount($documentType)
    {
        $manager = $this->getManager('monitoring', false);
        $repository = $manager->getRepository($documentType);
        $search = $repository->createSearch()->addQuery(new MatchAllQuery());

        return $this->parseResults($repository->execute($search, Repository::RESULTS_RAW));
    }

    /**
     * Returns hits total count from raw response.
     *
     * @param array $results
     *
     * @return mixed
     */
    private function parseResults($results)
    {
        return $results['hits']['total'];
    }
}
