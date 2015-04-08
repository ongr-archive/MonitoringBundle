Setup
=====

Step 1: Install Monitoring bundle
---------------------------------

Monitoring bundle is installed using `Composer`_.

.. code:: bash

    php composer.phar require ongr/monitoring-bundle "dev-master"

Step 2: Enable Monitoring bundle
--------------------------------

Monitoring bundle depends on ``ElasticsearchBundle`` therefore ``ElasticsearchBundle`` must be enabled before ``MonitoringBundle``.

Enable Monitoring bundle in your AppKernel:

.. code:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = return [
           ...
           new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
           new ONGR\MonitoringBundle\ONGRMonitoringBundle(),
           ...
       ];
    }

Step 3: Add configuration
-------------------------

.. code-block:: yaml

    ongr_elasticsearch:
        connections:
            default:
                hosts:
                    - { host: 127.0.0.1:9200 }
                index_name: ongr-monitoring-bundle-test
                settings:
                    refresh_interval: -1
                    number_of_replicas: 0
                    number_of_shards: 1
        managers:
            monitoring:
                connection: default
                debug: true
                mappings:
                    - ONGRMonitoringBundle
    ongr_monitoring:
        es_manager: monitoring
        commands:
            commands:
                - ongr:monitoring:metrics:collect
        metric_collector:
            metrics:
                document_count:
                    - { name: foo, document: es.manager.monitoring.product }

This configuration will enable logging of ``ongr:monitoring:metrics:collect`` command events and metric ``document_count`` metric collection for repository ``es.manager.monitoring.product``.

Step 4: User your new bundle
----------------------------

Usage documentation for Monitoring bundle is available `here <usage.rst>`_.

.. _Composer: https://getcomposer.org
