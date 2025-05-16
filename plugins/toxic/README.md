# Toxic_XH

Toxic_XH facilitates to have enhanced TOCs (table of contents) on your site,
which add features that are not available via the built-in `toc()` and
`li()` of CMSimple_XH, similar to those offered by the old AdvancedTOC and xtoc add-ons.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Toxic_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.
Toxic_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/toxic_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `toxic/` to your server into the `plugins/`
   directory of CMSimple_XH.
1. Set write permissions to the subdirectories `css/`, `config/` and
   `languages/`.
1. Browse to `Plugins` → `Toxic` in the back-end, and
   check whether all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website. Go to `Plugins` → `Toxic`.

You can change the default settings of Toxic_XH under `Config`.  Hints for the
options will be displayed when hovering over the help icon with your mouse.

Localization is done under `Language`.  You can translate the character strings
to your own language if there is no appropriate language file available, or
customize them according to your needs.

The look of Toxic_XH can be customized under `Stylesheet`.

## Usage

Toxic_XH only works for templates which use the standard CMSimple_XH menu
functions; custom menu functions which are used by some templates are not
supported.

To enable the enhanced Toxic_XH features, you need to replace the standard menu
functions with the drop-in replacements of Toxic_XH, i.e. replace:

    <?=toc(…)?>

with

    <?=toxic(…)?>

and/or

    <?=li(…)?>

with

    <?=toxic_li(…)?>

and/or

    <?=submenu(…)?>

with

    <?=toxic_submenu(…)?>

This enables the settings in the page data tab `Toxic` which you can define
for each individual page.

You can enter a catogory for any page, which means that this very page is the
first page of the category.  The category itself is put as separate menu item
into the menu and has purely informational or visual character, but not
functional (for instance, the category is not a link).  Each category item has
the CSS class `toxic_category`, which you can use to style it.  Note that the
category entry accepts arbitrary HTML markup, so you could use an image
instead of text for the category.

You can choose an individual CSS class for each menu item, which will be
available on the respective list item (`<li>`) in addition to the
CMSimple_XH standard CSS class `sdoc`/`sdocs`/`doc`/`docs`.
You can then add respective rules to the stylesheet of the template,
to design the menu item according to your wishes.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/toxic_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Toxic_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Toxic_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Toxic_XH. If not, see https://www.gnu.org/licenses/.

Copyright 1999-2009 Peter Harteg<br>
Copyright 2014 [The CMSimple_XH developers](https://cmsimple-xh.org/?The_Team)<br>
Copyright © Christoph M. Becker

## Credits

The plugin icon is designed by [new mooon](https://code.google.com/u/newmooon/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the
[CMSimple_XH Forum](http://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Especially, I like to thank *manu*, who triggered resuming of the plugin
developement via a question regarding `toc()`.

And last but not least many thanks to [Peter Harteg](http://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
