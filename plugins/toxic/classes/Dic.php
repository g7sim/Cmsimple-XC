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

namespace Toxic;

use Plib\SystemChecker;
use Plib\View;
use Toxic\Model\Pages;
use XH\Pages as XHPages;

class Dic
{
    public const VERSION = "1.0";

    public static function menuCommand(): MenuCommand
    {
        return new MenuCommand(self::conf(), self::pages());
    }

    public static function submenuCommand(): SubmenuCommand
    {
        return new SubmenuCommand(self::conf(), self::pages(), self::view());
    }

    public static function makeTabCommand(): TabCommand
    {
        global $plugin_cf;
        return new TabCommand($plugin_cf["toxic"], self::view());
    }

    public static function makeInfoCommand(): InfoCommand
    {
        global $pth;
        return new InfoCommand($pth["folder"]["plugins"] . "toxic/", new SystemChecker(), self::view());
    }

    private static function pages(): Pages
    {
        global $xh_publisher, $pd_router;
        return new Pages(new XHPages(), $xh_publisher, $pd_router);
    }

    /** @return array<string,string> */
    private static function conf(): array
    {
        global $cf, $plugin_cf;
        $conf = $plugin_cf["toxic"];
        $conf["menu_levelcatch"] = $cf["menu"]["levelcatch"];
        $conf["menu_levels"] = $cf["menu"]["levels"];
        $conf["menu_sdoc"] = $cf["menu"]["sdoc"];
        $conf["uri_separator"] = $cf["uri"]["seperator"];
        return $conf;
    }

    private static function view(): View
    {
        global $pth, $tx, $plugin_tx;
        $lang = $plugin_tx["toxic"];
        $lang["submenu_heading"] = $tx["submenu"]["heading"];
        return new View($pth["folder"]["plugins"] . "toxic/views/", $lang);
    }
}
