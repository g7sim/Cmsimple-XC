Views
=====

You may have already heard about the
`model–view–controller <https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller>`_
design pattern, which is popular for developing Web applications for many years.
In this section we focus on the view component, and although this is
interpreted rather differently by different application frameworks
regarding the details, the basics are likely always the same:
producing (aka. rendering) HTML.

Let us start with an excerpt from the old Online plugin by Amir
(version 1.0.7, `admin.php`):

.. code-block:: php
    $o.='
    <br>
    <table style="width: 95%; border: 12px outset #0000FF" cellSpacing="0" cellPadding="4" border="0" id="table1">
            <tr>
                <td style="border-bottom:12px solid #6699FF; text-align: center; padding-left:4px; padding-right:4px; padding-top:1px; padding-bottom:1px" colSpan="2">
                <h4>Online Plugin by Amir</h4>
                </td>
            </tr>
            <tr>
                <td align="left">Version</td>
                <td align="left">v1.07</td>
            </tr>
            <tr>
                <td align="left">Copyright</td>
                <td align="left">Amir</td>
            </tr>
            <tr>
                <td align="left" height="28">Email</td>
                <td align="left" height="28"><a href="mailto:a_galanti@yahoo.com">
                a_galanti@yahoo.com</a></td>
            </tr>
            <tr>
                <td align="left">Website</td>
                <td align="left">
                <a target="_blank" href="http://urceleb.oniton.com/">
                urceleb.oniton.com</a></td>
            </tr>
        </table>';

While it is obviously possible to write even complex HTML as literal PHP
string, it is harder to read (and edit) compared to an HTML file
(assuming you are using a decent code editor):

.. code-block:: html
  <br>
  <table style="width: 95%; border: 12px outset #0000FF" cellSpacing="0" cellPadding="4" border="0" id="table1">
    <tr>
      <td style="border-bottom:12px solid #6699FF; text-align: center; padding-left:4px; padding-right:4px; padding-top:1px; padding-bottom:1px" colSpan="2">
        <h4>Online Plugin by Amir</h4>
      </td>
    </tr>
    <tr>
      <td align="left">Version</td>
      <td align="left">v1.07</td>
    </tr>
    <tr>
      <td align="left">Copyright</td>
      <td align="left">Amir</td>
    </tr>
    <tr>
      <td align="left" height="28">Email</td>
      <td align="left" height="28">
        <a href="mailto:a_galanti@yahoo.com">a_galanti@yahoo.com</a>
      </td>
    </tr>
    <tr>
      <td align="left">Website</td>
      <td align="left">
        <a target="_blank" href="http://urceleb.oniton.com/">urceleb.oniton.com</a>
      </td>
    </tr>
  </table>

Now we could put this into an HTML file (say `view.html`),
and use it instead of the literal string:

.. code-block:: php
    $o .= file_get_contents($pth["folder"]["plugin"] . "view.html");

We have just created a view!
But should we really hard-code the version number in that HTML file?
And what about internationalization?
"Version", amongst other literal strings, works for English and German,
and some other languages, but should be "версия" in Russian, for instance.
This is where the `View` class is helpful.
Since this class works with PHP *template* files, let us rename `view.html` to `view.php`,
and change the respective table row:

.. code-block:: php
    <tr>
      <td align="left"><?=$this->text("version")?></td>
      <td align="left"><?=$version?></td>
    </tr>

And we change the respective code in `admin.php`:

.. code-block:: php
    $view = new View($pth["folder"]["plugin"], $plugin_tx["online"]);
    $o .= $view->render("view", ["version" => "v1.07"]);

Okay, what is going on here?
First, we create a new `View` object, tell it where to look for templates,
and also the available language strings of the plugin.
Then we "render" the template `view.php`, and pass in an arbitrary set of
variables (using `extract()` under the hood).

Since the plugin is old, and does not work with recent PHP and CMSimple_XH versions,
Alice takes over the maintainership.
Instead of hard-coding this in the `view.php` template, she wants to pass
the information as variable, and changes `view.php`:

.. code-block:: php
    <tr>
      <td align="left">Copyright</td>
      <td align="left"><?=$authors?></td>
    </tr>

and `admin.php`:

.. code-block:: php
    $view = new View($pth["folder"]["plugin"], $plugin_tx["online"]);
    $o .= $view->render("view", [
        "version" => "v1.07",
        "authors" => "Amir & Alice",
    ]);

When inspecting the generated HTML, Alice notices that the `&` is not
properly escaped as `&amp`, but instead of changing the value in `admin.php`,
she changes `view.php`:

.. code-block:: php
    <tr>
      <td align="left">Copyright</td>
      <td align="left"><?=$this->esc($authors)?></td>
    </tr>

And this is what you are expected to do: escape all *string* values in view templates,
unless they are already proper HTLM (e.g. the return value of `newbox()`),
in which case you want to clarify this by calling `View::raw()`.
