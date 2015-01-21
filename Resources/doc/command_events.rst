Command events logging
----------------------

Logging will be enabled only for commands listed under ``commands`` configuration node.

Command events monitoring configuration example:

.. code-block:: yaml

    ongr_monitoring:
        es_manager: monitoring
        commands:
            repository: es.manager.monitoring.event
            commands:
                - ongr:monitoring:metrics:collect

``repository`` - ElasticSearchBundle repository service.

All mapped documents has repository service.

e.g. ``es.manager.*manager_name*.*lowercase_document_name*``


Events
======

There are three command events ``console.command``, ``console.terminate`` and ``console.exception``

**console.command**

Before executing command ``console.command`` event is fired.

========= ========================
**Field** **Value**
--------- ------------------------
command   Command name
arguments Command arguments
started   Time command has started
status    *started*
========= ========================

**console.terminate**

After command is executed ``console.terminate`` event is fired.

========= =========================
**Field** **Value**
--------- -------------------------
ended     Time command has ended
status    *completed*
========= =========================

**console.exception**

Event is fired when command throws unhandled exception.

========= ========================
**Field** **Value**
--------- ------------------------
ended     Time command has ended
status    *exception*
message   Exception message
========= ========================
