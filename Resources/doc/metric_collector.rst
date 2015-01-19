Metric collector
----------------

Configuration
=============

Document count metrics can be collected on ES documents loaded by specified manager.

Example document count metric collector configuration:

.. code-block:: yaml

   ongr_monitoring:
        es_manager: monitoring
        repository: es.manager.monitoring.metric
        metrics:
            document_count:
                - { name: foo, document: es.manager.monitoring.product }
                - { name: bar, document: es.manager.monitoring.event }
            foo_product:
                - { name: foo_product, document: es.manager.monitoring.product }

``repository`` - ElasticSearchBundle repository service.

All mapped documents has repository service.

e.g. ``es.manager.*manager_name*.*lovercased_document_name*``

Collecting metrics
==================

To collect all metrics run ``app/console ongr:monitoring:metrics:collect``

To collect specific metric ``app/console ongr:monitoring:metrics:collect --metric=foo``

Custom metrics
==============

Full example can be found in ``Tests\fixture\Acme\TestBundle``.

Add metric service definition to your bundles ``Resources\config\services.yml`` and add tag ``ongr_monitoring.metric``.

.. code-block:: yaml

    parameters:
        acme_demo.metric.foo_product.class: ONGR\MonitoringBundle\Tests\app\fixture\Acme\TestBundle\Metric\FooProduct
    services:
        acme_demo.metric.foo_product:
            class: %acme_demo.metric.foo_product.class%
            arguments:
                - @ongr_monitoring.es_manager
            tags:
                - { name: ongr_monitoring.metric, metric: foo_product }


Create class which implements ``MetricInterface``.

Example:

.. code-block:: php

    class FooProduct implements MetricInterface
    {
        /**
         * @param Manager $manager
         * @param string  $name
         * @param string  $repository
         */
        public function __construct($manager, $name = null, $repository = null)
        {
            $this->manager = $manager;
            $this->name = $name;
            $this->repository = $repository;
        }

        /**
         * Get metric value.
         *
         * @return float
         */
        public function getValue()
        {
            $repository = $this->repository;

            /** @var Search $search */
            $search = $repository->createSearch()->addQuery(new MatchAllQuery());
            $results = $repository->execute($search, Repository::RESULTS_RAW_ITERATOR);

            return $results->getTotalCount();
        }

        /**
         * Get metric name.
         *
         * @return string
         */
        public function getName()
        {
            return 'foo_product';
        }
    }


