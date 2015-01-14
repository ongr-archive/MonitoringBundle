Metric collector
----------------

Configuration
=============

Document count metrics can be collected on ES documents loaded by specified manager.

Example document count metric collector configuration:

.. code-block:: yaml

   ongr_monitoring:
        es_manager: monitoring
        metric_collectors:
            repository: es.manager.monitoring.metric
            document_count:
                - { name: foo, document: AcmeTestBundle:Product }
                - { name: bar, document: ONGRMonitoringBundle:Metric }

``repository`` - ElasticSearchBundle repository service.

All mapped documents has repository service.

e.g. ``es.manager.*manager_name*.*lovercased_document_name*``

Collecting metrics
==================

To collect all metrics run ``app/console ongr:monitoring:metrics:collect``

To collect specific metric ``app/console ongr:monitoring:metrics:collect --metric=foo``
