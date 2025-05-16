Requests
========

Web applications are about processing requests:
a request comes in, is processed, and a response is sent back.
This matches the
`input-process-output model <https://en.wikipedia.org/wiki/IPO_model>`_

To be able to retrieve the actual details of the request,
PHP offers a set of super-globals (e.g. `$_GET` and `$_POST`),
and CMSimple_XH also provides several global variables (e.g. `$admin` and `$su`).

Let us start with an excerpt from Qualifire's guestbook plugin
(version 08-beta, `index.php`):

.. code-block:: php
    $gbname = isset($_POST['gbname']) ? $_POST['gbname'] : $_GET['gbname'];
    $gbemail = isset($_POST['gbemail']) ? $_POST['gbemail'] : $_GET['gbemail'];
    $gbwebsite = isset($_POST['gbwebsite']) ? $_POST['gbwebsite'] : $_GET['gbwebsite'];
    $gbtitle = isset($_POST['gbtitle']) ? $_POST['gbtitle'] : $_GET['gbtitle'];
    $gbmessage = isset($_POST['gbmessage']) ? $_POST['gbmessage'] : $_GET['gbmessage'];
    $gbpage = isset($_POST['page']) ? $_POST['page'] : $_GET['page'];

Note that it may not be the best idea to accept either post or get parameters,
but let us ignore that for now.

The problem with this approach of accessing the input values is
that `$_POST` as well as `$_GET` may contain arbitrarily nested arrays.
For instance, the query string `foo[]=bar&foo[]=baz` will yield

.. code-block:: php
    $_GET == ["foo" => ["bar", "baz"]]

Thus, in the code above we don't know whether the variables
(e.g. `gbname`) hold strings or arrays.
To avoid strange or even buggy behavior in the following,
we need to additionally check that these variables actually hold
strings, and need to error out otherwise.
So we could add something like:

.. code-block:: php
    if (
        !is_string($gbname) ||
        !is_string($gbemail) ||
        !is_string($gbwebsite) ||
        !is_string($gbtitle) ||
        !is_string($gbmessage) ||
        !is_string($gbpage)
    ) {
        // handle error
    }

Quite an amount of code just to properly get the expected input strings,
and we did not even cater to whitespace *around* the string values,
which is not uncommon, when the values have been submitted via an
HTML form.
Since especially handling of string inputs is so common,
Plib_XH offers a simplification:

.. code-block:: php
    $request = Request::current();
    $gbname = $request->post("gbname") ?? $request->get("gbname");
    $gbemail = $request->post("gbemail") ?? $request->get("gbemail");
    $gbwebsite = $request->post("gbwebsite") ?? $request->get("gbwebsite");
    $gbtitle = $request->post("gbtitle") ?? $request->get("gbtitle");
    $gbmessage = $request->post("gbmessage") ?? $request->get("gbmessage");
    $gbpage = $request->post("gbpage") ?? $request->get("gbpage");
    if (!isset($gbname, $gbemail, $gbwebsite, $gbtitle, $gbmessage, $gbpage)) {
        // handle error
    }

`Request::current()` gives us a `Request` object, which we can use to
retrieve the input strings (which are already trimmed); if there is no
such string, `null` is returned, so we can use the null-coalescing (`??`)
operator of PHP, and the combined `isset()` to simplify the code.

A couple of lines further down in the guestbook plugin, we have:

.. code-block:: php
    header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']); exit;

This is a basic implementation of the
`Post/Redirect/Get <https://en.wikipedia.org/wiki/Post/Redirect/Get>`_
pattern.
The problem with this code (besides that it is not particularly readable)
is that it does not cater to HTTPS, nor to a certain port,
nor some other details, such as suppressing the `?` in case of an empty
query string.

All this is nicely encapsulated in the Plib_XH `Url` object:

.. code-block:: php
    header("Location: " . $request->url()->absolute()); exit;

Sometimes we want to add an additional query parameter when doing such
a redirect, so we can display a success message to the user:

.. code-block:: php
    header("Location: " . $request->url()->with("gbresult", "success")->absolute()); exit;
