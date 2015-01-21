Metric collector
----------------

One can register metrics on CollectorService and collect metrics using command ``ongr:monitoring:metrics:collect``. See below for command usage examples.

DocumentCount metric
~~~~~~~~~~~~~~~~~~~~

Bundle has DocumentCount metric by default. Its purpose is to log document count for given ES Document.

Usage example:

.. code-block:: yaml

   ongr_monitoring:
        es_manager: monitoring
        metrics:
            document_count:
                - { name: foo, document: es.manager.monitoring.product }


To collect (create record in ES) metric ``app/console ongr:monitoring:metrics:collect --metric=foo``

Custom metrics
~~~~~~~~~~~~~~

One can easily add custom metrics to collector service. Full example can be found in ``Tests\fixture\Acme\TestBundle``.

Add metric service definition to your bundles ``Resources\config\services.yml`` and add tag ``ongr_monitoring.metric``.

Metric service configuration:

.. code-block:: yaml

    parameters:
        acme_test.metric.foo_product.class: ONGR\MonitoringBundle\Tests\app\fixture\Acme\TestBundle\Metric\FooProduct
    services:
        acme_test.metric.foo_product:
            class: %acme_test.metric.foo_product.class%
            arguments:
                - @ongr_monitoring.es_manager
            tags:
                - { name: ongr_monitoring.metric, metric: foo_product }


Create metric class which implements ``MetricInterface``.

.. code-block:: php

    interface MetricInterface
    {
        public function getValue():float;

        public function getName():string;
    }

If you define constructor in you metric class ``manager``, ``metric_name`` and ``repository`` values from config will be passed.


Custom metric example:

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


Registering multiple metrics
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To register multiple metrics with ``CollectorService`` configuration should look like:

.. code-block:: yaml

   ongr_monitoring:
        es_manager: monitoring
        repository: es.manager.monitoring.metric
        metrics:
            document_count:
                - { name: foo, document: es.manager.monitoring.product }
            foo_product:
                - { name: foo_product, document: es.manager.monitoring.product }


Collecting metrics
~~~~~~~~~~~~~~~~~~

To collect all metrics run ``app/console ongr:monitoring:metrics:collect``

To collect specific metric ``app/console ongr:monitoring:metrics:collect --metric=foo``
