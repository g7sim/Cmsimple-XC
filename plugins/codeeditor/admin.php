<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Codeeditor_XH.
 *
 * Codeeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Codeeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codeeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

use Codeeditor\Dic;
use Plib\Request;

if (!defined("CMSIMPLE_XH_VERSION")) {
    http_response_code(403);
    exit;
}

/** @return list<string> */
function codeeditor_getThemes(): array
{
    return Dic::editor()->getThemes();
}

/**
 * @var string $admin
 * @var string $o
 * @var array<string,array<string,string>> $plugin_cf
 */

XH_registerStandardPluginMenuItems(false);
XH_registerPluginType("editor", "codeeditor");
if ($plugin_cf["codeeditor"]["enabled"]) {
    Dic::mainCommand()(Request::current())();
}
if (XH_wantsPluginAdministration("codeeditor")) {
    $o .= print_plugin_admin("off");
    switch ($admin) {
        case "":
            $o .= Dic::infoCommand()()();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
