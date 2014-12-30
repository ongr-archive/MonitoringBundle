Metric collector
----------------

**Metric collector configuration.**

Document count metrics can be collected on ES documents loaded by specified manager.

Example document count metric collector configuration:

.. code-block:: yaml

   ongr_monitoring:
       es_manager:
           monitoring
       metric_collectors:
           document_count:
               - { name: foo, document: AcmeTestBundle:Product }

To collect all metrics run ``app/console ongr:monitoring:metrics:collect``

To collect specific metric ``app/console ongr:monitoring:metrics:collect --metric=foo``
