Usage
=====

Commands
--------

1. Enable command events logging
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To enable metric collector command events logging your configuration should look like:

.. code-block:: yaml

    ongr_monitoring:
        es_manager: default
        commands:
            commands:
                - ongr:monitoring:metrics:collect

This will create and update record in ES during command execution `more <command_events.rst>`_.

Metrics
-------

1. Document count metric
~~~~~~~~~~~~~~~~~~~~~~~~

DocumentCount metric can log specific repositories document count.

Configuration:

.. code-block:: yaml

    ongr_monitoring:
        es_manager: default
        metric_collector:
            metrics:
                document_count:
                    - { name: foo, document: es.manager.default.product }

This will create a record in ES with es.manager.default.product repository item count.

2. Adding custom metric
~~~~~~~~~~~~~~~~~~~~~~~

Add logic to ``getValue`` method. Result of this method will be recorded to ES.

2.1 Create custom metric class

.. code-block:: php

    class WhiteWine implements MetricInterface
    {
        /**
         * @var string
         */
        protected $name;

        /**
         * @var Manager
         */
        protected $manager;

        /**
         * @var string
         */
        protected $repository;

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
            $search = $repository->createSearch()->addQuery(new TermQuery('wine_colour', 'White'));
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
            return $this->name;
        }
    }

``manager``, ``name`` and ``repository`` is injected from configuration.

2.2 Add metric service

.. code-block:: yaml

    parameters:
        acme_test.metric.white_wine.class: Acme\TestBundle\Metric\WhiteWine
    services:
        acme_test.metric.white_wine:
            class: %acme_test.metric.white_wine.class%
            arguments:
                - @ongr_monitoring.es_manager
            tags:
                - { name: ongr_monitoring.metric, metric: white_wine }

.. note::

    Service must be tagged with ``ongr_monitoring.metric``. ``metric`` value will be used in config as metric node.

2.3 Add configuration

.. code-block:: yaml

    ongr_monitoring:
        es_manager: monitoring
        metric_collector:
            metrics:
                white_wine:
                    - { name: white_whine, document: es.manager.monitoring.product }


2.4 Collect metric

Collect metric:

.. code-block:: bash

    app/console ongr:monitoring:metrics:collect --metric=white_whine

