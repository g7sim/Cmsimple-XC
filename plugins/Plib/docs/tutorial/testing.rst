Developer Tests
===============

When you are developing a CMSimple_XH plugin,
you are likely doing a lot of manual testing.
As the complexity of the plugin increases,
manual testing takes more and more time,
up to the point where you likely stop testing
all functionality, and rectify this by not touching
existing code any longer, although you sometimes may have
the feeling that this old code needs some overhaul.
Even worse, sometimes you may be forced to touch that
code, because there is a bug, or some new PHP version
requires you to do some changes.

To reduce the amount of manual testing, you can apply some
`Test automation <https://en.wikipedia.org/wiki/Test_automation>`_.
The closest to manual testing are automated end-to-end tests,
which you can create using `Selenium <https://www.selenium.dev/>`_,
for instance.
However, you still need to set up a testing environment up-front,
e.g. you need to add some pages to your CMSimple_XH installation
with the respective plugin calls, and automated end-to-end
tests for back-end functionality require to log-in first.
But maybe worse, these tests are slow;
a single test might take a second, and if your plugin is complex,
you may need dozens or even hundreds of tests, to test only the most
relevant code paths.
While this might be okay for final testing prior to a release
(basically what a QA department might do),
running these tests during development inevitably slows you down.

The solution to this problem are developer tests;
these are sometimes called unit tests,
but we feel that this name is too overloaded, so avoid it.
The important point of developer tests is that they exercise
all relevant code paths
(ideally every line of code, although that might not be sufficient),
but are still fast, i.e. run in a few seconds at most,
so that you can (and will) them run often,
not only after every couple of hours.

However, you cannot write sensible developer tests for arbitrary code.
Let us look at an excerpt from the Online plugin
(version v.1.07, `index.php`):

.. code-block:: php
    $time = time();
    function gonline() {
    GLOBAL $su, $pth, $plugin_tx,$plugin_cf,$_SERVER,$ip,$time;
    // code omitted
        if ($time < $savedtime + ($minutes * 60)) 
            {
            // code omitted
            }
        }
    // code omitted
    return $o;
    }

You cannot write a developer test for `gonline()`,
unless you are able to somehow force the `time()`
call to return a predetermined value.
While there are PHP extensions which allow to stub
even built-in PHP functions (such as `uopz` and `runkit7`),
these usually yield hard to maintain test code,
and we do not want to spend more time on our test code
than on our production code.
Furthermore, these extensions often require internal changes
for each minor PHP version, so may not be ready in time to
test against the latest PHP version.
The latter issue can be solved by using the namespace fallback,
what requires the code to be tested to be namespace
(what is a good idea, anyway),
but that still yields hard to maintain test code.

The `time()` function is only an example;
you need to stub anything that yields non-deterministic results
(such as `random_bytes()`), or has side effects (such as `mail()`),
and you want to stub anything that is slow (e.g. `password_hash()`).
Database access may also be slow, but that is usually not a concern
for CMSimple_XH plugins, but even the filesystem might be too slow
when there are many tests
(`vfsStream <https://github.com/bovigo/vfsStream>`_ may be helpful,
but does not support all file operations).

And there is another issue, not rarely found in CMSimple_XH plugins,
namely the mix of declarations and statements in a single file,
e.g. some code which executes directly and some functions.
If this code should be tested multiple times,
you cannot use `include_once`, but using `include` more than once
already raises a fatal PHP error ("foo already declared").

Thus, to be amenable to developer testing, code needs to be written
in a certain way (or more generally, needs to avoid several pitfalls),
and besides the generally useful
`separation of concerns <https://en.wikipedia.org/wiki/Separation_of_concerns>`_,
objects come in very handy,
because they can either be purpose-built to postpone side-effects
(such as `Response`),
or can be replaced with another type compatible object,
which has different behavior (such as `Request`).

To come back to our hard to test Online plugin excerpt,
this is how you can write it with testability in mind:

.. code-block:: php
    function gonline() {
        return gonline_internal(Request::current())();
    }

    function gonline_internal(Request $request): Response
    {
        global $su, $pth, $plugin_tx, $plugin_cf;
        $time = $request->time();
        // code omitted
            if ($time < $savedtime + ($minutes * 60))
                {
                // code omitted
                }
            }
        // code omitted
        return Response::create($o);
    }

While you still can hardly write a developer test for `gonline()`
(what is not a real problem, given that the function is trivial),
you can now write a developer test for `gonline_internal()`
by passing in a test double which always returns a predetermined
value from `::time()`.
Since Plib_XH is written with testability in mind,
and there is often the need to fake a `Request`,
there is already the `FakeRequest` class, which you can set up like:

.. code-block:: php
    $request = new FakeRequest(["time" => "1742155487"]);

We do not present complete test code, because that obviously
depends on the testing framework of your choice,
and also because it would still be somewhat fiddly due to
the many global variables and the filesystem access code
which is embedded in the function.
