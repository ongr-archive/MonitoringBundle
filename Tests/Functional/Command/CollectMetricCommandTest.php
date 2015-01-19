<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\MonitoringBundle\Tests\Functional\Command;

use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\MonitoringBundle\Command\CollectMetricCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CollectMetricCommandTest extends ElasticsearchTestCase
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
     * Test collect all metrics command case.
     */
    public function testMetricNotSet()
    {
        $command = new CollectMetricCommand();

        $command->setContainer($this->getContainer());
        $app = new Application();
        $app->add($command);

        $commandToTest = $app->find('ongr:monitoring:metrics:collect');
        $commandTester = new CommandTester($commandToTest);
        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $this->assertEquals(
            3,
            $this->getDocumentCount('ONGRMonitoringBundle:Metric'),
            'Should be created 3 metric records'
        );
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
