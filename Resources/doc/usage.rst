Usage
-----

Enable Monitoring bundle in your ``AppKernel.php``:

``ONGRElasticsearchBundle`` must be enabled before ``ONGRMonitoringBundle``.

.. code-block:: php

   public function registerBundles()
   {
       return [
           ...
           new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
           ...
           new ONGR\MonitoringBundle\ONGRMonitoringBundle(),
           ...
       ];
   }


Example yaml configuration:

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
                   - AcmeTestBundle
                   - ONGRMonitoringBundle
   ongr_monitoring:
        es_manager: monitoring
        commands:
            repository: es.manager.monitoring.event
            commands:
                - ongr:monitoring:metrics:collect
        metric_collector:
            repository: es.manager.monitoring.metric
            metrics:
            document_count:
                - { name: foo, document: es.manager.monitoring.product }
                - { name: bar, document: es.manager.monitoring.event }
            foo_product:
                - { name: foo_product, document: es.manager.monitoring.product }


Monitoring ES manager must have mappings for ONGRMonitoringBundle and for bundles which will be monitored.
