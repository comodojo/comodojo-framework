Install Comodojo
================

.. _comodojo: https://github.com/comodojo/comodojo
.. _composer: https://getcomposer.org/
.. _comodojo/comodojo-installer: https://github.com/comodojo/comodojo-installer
.. _comodojo/comodojo: https://github.com/comodojo/comodojo

Comodojo can be installed via `composer`_, using `comodojo/comodojo`_ project's package.

Requirements
************

To work properly, comodojo requires an apache webserver (nginx will also be supported ) with PHP >=5.6.0.

Installing via composer
***********************

.. warning:: This procedure is not complete, unstable, untested. In other words, there are no chances it will work.

A new comodojo/comodojo project can be created with:

    composer create-project comodojo/comodojo myinstallationpath

The web root of your server shoud point to myinstallationpath/public.

More details will come in near future.
