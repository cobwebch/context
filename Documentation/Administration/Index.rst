.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _administration:

Administration
--------------


.. _administration-syntax-base:

Base syntax
^^^^^^^^^^^

Contexts are defined using TypoScript. This is convenient because
contexts need to be inherited along the page tree. The syntax is the
following:

.. code:: typoscript

	config.tx_context.foo = bar

When the context is loaded this will create an entry in the context
array with the key "foo" and the value "bar". Contexts can be
retrieved using :ref:`expressions <expressions:start>`, in any extension that supports
expressions. The contexts are loaded in the extra data of the
expressions parser. Thus to retrieve a value from the context you
would use a syntax like:

.. code:: text

	extra:foo

This will return "bar".


.. _administration-syntax-extended:

Extended syntax
^^^^^^^^^^^^^^^

It is meant for contexts to be able to reference a record from some
table. For this contexts support the following syntax:

.. code:: typoscript

	config.tx_context.newscat = sys_category:3

This means that the context's entry value is 3, but it's actually the
system category with uid = 3.

When the context is loaded (in particular into the expression parser's
extra data) the part with the table name is stripped. So the following
expression:

.. code:: text

	extra:newscat

will return "3".

This extended syntax is meant to be used in the future BE module so
that users could select a table and then a record from that table and
this would create a context entry. The table name would be written in
the context, so that it can be retrieved upon editing.

This extended syntax can already be used when creating references to
database values, just to make it clear to what table it is related,
even if there's no helper BE module.


.. _administration-developer:

Developer information
^^^^^^^^^^^^^^^^^^^^^

By default contexts are loaded only into the extra data of the
expressions parser. However a hook is available to load the context
into something else. Hook usage should be registered using:

.. code:: php

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['context']['contextStorage'][] = '...';

The designated class must implement the :code:`\Cobweb\Context\ContextStorageInterface`
interface which consists of a single method:

storeContext
  This receives the context as an array of key-value
  pairs. It is not expected to return anything.


.. _administration-developer-api:

API
"""

Class :code:`\Cobweb\Context\ContextLoader` offers a method
for directly getting a context value. Given a context like:

.. code:: typoscript

	config.tx_context.foo.pid = 42

this value could be retrieved in PHP with the simple call:

.. code:: php

	try {
		$value = \Cobweb\Context\ContextLoader::getContextValue('foo|pid');
	}
	catch (Exception $e) {
		// Do something with the exception
	}

.. note::

   Don't use dots in the arguments passed in the call (as would
   be usual with values coming from TypoScript), i.e. in the example
   above it is :code:`foo|pid` and not :code:`foo.|pid`.

The :code:`getContextValue()` method will throw an exception if the
requested value is not found, so any call to it should be wrapped in a
try/catch block.
