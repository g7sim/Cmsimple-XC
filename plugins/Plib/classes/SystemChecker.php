<?php

/**
 * Copyright (C) Christoph M. Becker
 *
 * This file is part of Plib_XH.
 *
 * Plib_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plib_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plib_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Plib;

/**
 * Typical predicates for system checks
 *
 * @final
 */
class SystemChecker
{
    public function checkVersion(string $actual, string $minimum): bool
    {
        return version_compare($actual, $minimum) >= 0;
    }

    public function checkExtension(string $extension): bool
    {
        return extension_loaded($extension);
    }

    public function checkGdFreetype(): bool
    {
        return function_exists("gd_info") && gd_info()['FreeType Support'];
    }

    public function checkGdPng(): bool
    {
        return function_exists("imagetypes") && (imagetypes() & IMG_PNG);
    }

    /** @since 1.1 */
    public function checkPlugin(string $plugin, ?string $version = null): bool
    {
        $pluginVersion = XH_pluginVersion($plugin);
        if ($version === null) {
            return (bool) $pluginVersion;
        }
        return version_compare($pluginVersion, $version) >= 0;
    }

    public function checkWritability(string $path): bool
    {
        return is_writable($path);
    }
}
