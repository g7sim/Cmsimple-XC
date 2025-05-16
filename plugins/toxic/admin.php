<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Toxic_XH.
 *
 * Toxic_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Toxic_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Toxic_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

use Toxic\Dic;
use XH\PageDataRouter;

/**
 * @var string $admin
 * @var string $o
 * @var PageDataRouter $pd_router
 * @var array<string,array<string,string>> $plugin_tx
 * @var array{folder:array<string,string>,file:array<string,string>} $pth
 */

$pd_router->add_tab(
    $plugin_tx["toxic"]["label_tab"],
    $pth["folder"]["plugins"] . "toxic/toxic_view.php"
);
XH_registerStandardPluginMenuItems(false);
if (XH_wantsPluginAdministration("toxic")) {
    $o .= print_plugin_admin("off");
    switch ($admin) {
        case "":
            $o .= Dic::makeInfoCommand()()();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
