Responses
=========

In the previous chapter we already stated

    a request comes in, is processed, and a response is sent back.

For CMSimple_XH plugins, these reponses are often just strings,
either returned from a plugin call function, or appended to the output (`$o`)
in several other cases.
However, sometimes they are not strings.
We have already seen an example in the previous cahpter,
but let us consider the following excerpt from Michael Svarrer's gallery plugin
(version 0.9, `admin.php`):

.. code-block:: php
    if($gallery){
    // code omitted
    $o.=print_plugin_admin('on');
    // code omitted
    if($action=='upload'){
    // code omitted
    header("Location: ".$sn."?&gallery&admin=plugin_main&path=".$path."&action=view&error=".$error);exit;
    }
    // code omitted
    }

This code is executed outside of any function,
and the omitted code uses an awful lot of global variables.
This is generally bad practice, so let us rewrite to use a function:

.. code-block:: php
    if ($gallery) {
        $o .= process_gallery();
    }

    function process_gallery()
    {
        global $action, $sn, …;
        // code omitted
        $o .= print_plugin_admin('on');
        // code omitted
        if ($action == 'upload') {
            // code omitted
            header("Location: ".$sn."?&gallery&admin=plugin_main&path=".$path."&action=view&error=".$error);exit;
        }
        return $o;
    }

On `$action=='upload'` we end script execution without returning anything.
But in some other cases, the `process_gallery()` function would actually return a string.
In the type system of PHP the return value of `process_gallery()`
would be `string|never` as of PHP 8.1,
or for older version the slightly weaker `string|void`.
While this is not a particular problem per se,
Plib_XH offers a nice generalization, namely `Response`.

So with Plib_XH we can rewrite `process_gallery()`:

.. code-block:: php
    function process_gallery(): Response
    {
        global $action, $sn, …;
        // code omitted
        $o .= print_plugin_admin('on');
        // code omitted
        if ($action == 'upload') {
            // code omitted
            $url = $request->url()->with("admin", "plugin_main")->with("path", $path)
                ->with("action", "view")->with("error", $error);
            return Response::redirect($url->absolute());
        }
        return Response::create($o);
    }

So instead of returning `string|never`,
we now always return a `Response` object,
and declare the return type of the function accordingly.
This expresses our intent, enables PHP to report any violation of this contract
(e.g. when we forget to return a `Response` from some code path).
And it may even help PHP to optimize the code execution.

`Response::create()` creates a response with a string which will be output;
`Response::redirect()` creates a response which will trigger a redirect.

Note that `process_gallery()` is now free of side-effects
(the `exit` statement is gone), which is a useful property of functions,
which we are going to exploit in the next chapter.

However, at some point these side-effects have to occur;
otherwise the plugin could not fulfill its purpose.
This is done by invoking (aka. triggering) the `Response` in the global
scope:

.. code-block:: php
    if ($gallery) {
        $response = process_gallery();
        $o .= $response();
    }

This is an explicit version, to make it easier to understand
what is going on. Usually, you want to use the short version, though:

.. code-block:: php
    if ($gallery) {;
        $o .= process_gallery()();
    }

That is, call the function, and then immediately invoke the returned `Response`.

Now let us reconsider the `gblist()` function of Qualifire's guestbook plugin
(version 08-beta, `index.php`):

.. code-block:: php
    function gblist($gb_filename) {
        // code omitted
            if (!$fp = fopen($gbfile, 'a+')) {
                $t .= "<br><hr>ERROR: Cannot open file ($gbfile)<hr><br>";
            } else {
                // code omitted
                header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']); exit;
            }
        // code omitted
        return $t;
    }

We may want to use `Response`s here, too, but since `gblist()`
is a function implementing a plugin call, we have to return a `string`.
A simple and straight forward solution is to extract an internal
function:

.. code-block:: php
    /** @return string|never */
    function gblist(string $gb_filename)
    {
        return gllist_internal($gb_filename)();
    }

    function gblist_internal(string $gb_filename): Response
    {
        // code omitted
            if (!$fp = fopen($gbfile, 'a+')) {
                // $view has been created in the omitted code above
                $t .= $view->message("fail", "error_open", $gbfile);
            } else {
                // code omitted
                // $request has been created in the omitted code above
                return Response::redirect($request->url()->absolute());
            }
        // code omitted
        return Response::create($t);
    }

We also use `View::message()` (which wraps `XH_message`) for brevity
and internationalization purposes.
Anyhow, given that

    a request comes in, is processed, and a response is sent back.

we may want to make that explicit:

.. code-block:: php
    /** @return string|never */
    function gblist(string $gb_filename)
    {
        return gllist_internal(Request::current(), $gb_filename)();
    }

    function gblist_internal(Request $request, string $gb_filename): Response
    {
        // code omitted
            if (!$fp = fopen($gbfile, 'a+')) {
                // $view has been created in the omitted code above
                $t .= $view->message("fail", "error_open", $gbfile);
            } else {
                // code omitted
                return Response::redirect($request->url()->absolute());
            }
        // code omitted
        return Response::create($t);
    }
