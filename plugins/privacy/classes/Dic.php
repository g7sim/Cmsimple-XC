<?php

/**
 * Copyright (C) Christoph M. Becker
 *
 * This file is part of Privacy_XH.
 *
 * Privacy_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Privacy_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Privacy_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Privacy;

use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function makePrivacy(): Privacy
    {
        global $plugin_cf;
        return  new Privacy(
            $plugin_cf["privacy"],
            self::makeView()
        );
    }

    public static function makePluginInfo(): PluginInfo
    {
        global $pth;

        return new PluginInfo(
            "{$pth['folder']['plugins']}privacy/",
            new SystemChecker(),
            self::makeView()
        );
    }

    private static function makeView(): View
    {
        global $pth, $plugin_tx;

        return new View(
            $pth["folder"]["plugins"] . "privacy/views/",
            $plugin_tx['privacy']
        );
    }
}
