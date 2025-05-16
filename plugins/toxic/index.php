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

use Plib\Request;
use Toxic\Dic;
use XH\PageDataRouter;

if (!defined("CMSIMPLE_XH_VERSION")) {
    http_response_code(403);
    exit;
}

function toxic(?int $start = null, ?int $end = null): string
{
    return toc($start, $end, 'Toxic_li');
}

/** @param list<int> $ta */
function toxic_li(array $ta, int $st): string
{
    return Dic::menuCommand()(Request::current(), $ta, $st)();
}

function toxic_submenu(string $html = ""): string
{
    return Dic::submenuCommand()(Request::current(), $html)();
}

/**
 * @var PageDataRouter $pd_router
 */

$pd_router->add_interest("toxic_category");
$pd_router->add_interest("toxic_class");
