Overview
========

Plib_XH is a set of infrastructure classes to simplify
the development of `CMSimple_XH <https://www.cmsimple-xh.org/>`_ plugins.
Besides generally reducing code repetition across different plugins,
it focuses on encapsulating accesses to global variables
(which are a good part of the API of CMSimple_XH) and procedural
infrastructure functions (some provided by CMSimple_XH, and many by PHP),
so `automated testing <https://en.wikipedia.org/wiki/Test_automation>`_
is no longer tedious or even outright impossible.

Contrary to `Pfw_XH <https://github.com/cmsimple-xh/pfw_xh>`_, Plib_XH is not
a `framework <https://en.wikipedia.org/wiki/Software_framework>`_,
and as such does not impose a certain
`software architecture <https://en.wikipedia.org/wiki/Software_architecture>`_,
although it offers components which are suitable to implement
`model-view-controller <https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller>`_
like architectures.
