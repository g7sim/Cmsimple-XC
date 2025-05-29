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

namespace Codeeditor;

use Plib\SystemChecker;
use Plib\View;

class Dic
{
    /** @var Editor */
    private static $editor = null;

    public static function editor(): Editor
    {
        global $pth, $cf, $plugin_cf;

        if (self::$editor === null) {
            self::$editor = new Editor(
                $pth["folder"]["plugins"],
                $plugin_cf["codeeditor"]["theme"],
                $cf['filebrowser']['external'],
                self::view()
            );
        }
        return self::$editor;
    }

    public static function mainCommand(): MainCommand
    {
        return new MainCommand(self::editor());
    }

    public static function infoCommand(): InfoCommand
    {
        global $pth;

        return new InfoCommand(
            $pth["folder"]["plugins"] . "codeeditor/",
            new SystemChecker(),
            self::view()
        );
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "codeeditor/views/", $plugin_tx["codeeditor"]);
    }
}
