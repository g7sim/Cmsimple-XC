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

use Plib\Request;
use Plib\Response;
use Plib\View;
use Toxic\Model\Pages;

class SubmenuCommand
{
    use ListRendering;

    /** @var array<string,string> */
    private $conf;

    /** @var Pages */
    private $pages;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, Pages $pages, View $view)
    {
        $this->conf = $conf;
        $this->pages = $pages;
        $this->view = $view;
    }

    public function __invoke(Request $request, string $html): Response
    {
        if ($request->s() < 0) {
            return Response::create();
        }
        $tocArray = $this->pages->children($request->s(), (int) $this->conf["menu_levelcatch"]);
        if (count($tocArray) <= 0) {
            return Response::create();
        }
        if ($html === "") {
            $level = min(6, (int) $this->conf["menu_levels"] + 1);
            return Response::create("<h$level>" . $this->view->plain("submenu_heading") . "</h$level>\n"
                . $this->render($request, $tocArray));
        }
        return Response::create(sprintf($html, $this->view->plain("submenu_heading")) . "\n"
            . $this->render($request, $tocArray));
    }

    /** @param list<int> $ta */
    private function render(Request $request, array $ta): string
    {
        if (count($ta) === 0) {
            return "";
        }
        $o = "<ul class=\"submenu\">\n";
        foreach ($ta as $page) {
            $o .= $this->renderCategoryItem($page)
                . "<li class=\"{$this->renderClasses($request, $page)}\">"
                . $this->renderMenuItem($request, $page)
                . "</li>\n";
        }
        $o .= "</ul>\n";
        return $o;
    }
}
