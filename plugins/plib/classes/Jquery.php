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
 * Wraps the jQuery4CMSimple functionality
 *
 * @final
 */
class Jquery
{
    /** @var string */
    private $jqueryFolder;

    public function __construct(string $jqueryFolder)
    {
        $this->jqueryFolder = $jqueryFolder;
    }

    public function include(): void
    {
        include_once $this->jqueryFolder . "jquery.inc.php";
        include_jQuery();
    }

    public function includePlugin(string $name, string $path): void
    {
        include_once $this->jqueryFolder . "jquery.inc.php";
        include_jQueryPlugin($name, $path);
    }

    /** @since 1.7 */
    public function includeUi(): void
    {
        include_once $this->jqueryFolder . "jquery.inc.php";
        include_jQueryUI();
    }
}
