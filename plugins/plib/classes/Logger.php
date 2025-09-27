<?php

/**
 * Copyright (c) Christoph M. Becker
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
 * A thin wrapper around XH_logMessage
 *
 * @see https://dev-doc.cmsimple-xh.org/functions_8php.html#a8cd48cee4ab6a1fbd6a8436465484c1e
 * @since 1.7
 */
class Logger
{
    /** @var string */
    private $module;

    public function __construct(string $module)
    {
        $this->module = $module;
    }

    public function log(string $type, string $category, string $description): void
    {
        XH_logMessage($type, $this->module, $category, $description);
    }
}
