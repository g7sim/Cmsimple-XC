<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Coco_XH.
 *
 * Coco_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Coco_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Coco_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

// phpcs:disable PSR1.Files.SideEffects

use Coco\Dic;
use Plib\Request;

const COCO_VERSION = "2.1";

/** @param string|false $config */
function coco(string $name, $config = false, string $height = "100%"): string
{
    return Dic::makeCoco()(Request::current(), $name, (string) $config, $height)();
}

if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

/**
 * @var XH\PageDataRouter $pd_router
 * @var string $f
 * @var string $o
 */

$pd_router->add_interest("coco_id");

if ($f === "xh_loggedout") {
    $o .= Dic::makeMain()(Request::current())();
}
