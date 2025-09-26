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

    /** @deprecated Use {@see SystemChecker::checkGdFeature()} instead. */
    public function checkGdFreetype(): bool
    {
        return $this->checkGdFeature("FreeType");
    }

    /** @deprecated Use {@see SystemChecker::checkGdFeature()} instead. */
    public function checkGdPng(): bool
    {
        return $this->checkGdFeature("PNG");
    }

    /**
     * Wraps the most relevant features reported by gd_info()
     *
     * @param string $feature either `FreeType`, `GIF Read`, `GIF Create`,
     *                        `JPEG`, `PNG`, `WBMP`, `XPM`, `XBM`, `WebP`,
     *                        `BMP`, `TGA Read` or `AVIF`.
     *
     * @since 1.11
     */
    public function checkGdFeature(string $feature): bool
    {
        if (!function_exists("gd_info")) {
            return false;
        }
        $info = gd_info();
        return array_key_exists("$feature Support", $info) && $info["$feature Support"];
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
