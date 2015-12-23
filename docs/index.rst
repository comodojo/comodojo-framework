.. The Comodojo Framework documentation master file, created by
   sphinx-quickstart on Mon Dec 21 00:27:34 2015.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

The Comodojo Framework's docs
=============================

.. image:: _static/comodojo_logo.png

Comodojo is a PHP/js framework that provides a complete environment to
realize complex and easy-to-maintain web applications.

Instead of offer a common foundation classes to biuld a single web app, Comodojo
aims to be a pre-built environment where:

- a couple of logical releated function (or even a single one) can be realized by an application;
- an application publishes routes both on a server and client side, defining state-aware, linkable locations;
- applications can (and generally should) be deployed, updated, removed using composer; in addition, multiple applications can be packaged under the same composer package;
- internal role-mapper provides the connection between users and applications, exposing to user only the routes it should reach.

.. warning:: Code is under active development, there is no stable release and it may change without notice at any time.

.. note:: If you're interested in participating development, please send a message to marco.giovinazzi@comodojo.org.

Contents:

.. toctree::
   :maxdepth: 2

   status
   installation
   authentication
   applications

Indices and tables
==================

* :ref:`genindex`
* :ref:`modindex`
* :ref:`search`
