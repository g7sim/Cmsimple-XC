<?php

/**
 * Copyright 1999-2009 Peter Harteg
 * Copyright 2014 The CMSimple_XH developers <https://cmsimple-xh.org/?The_Team>
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

trait ListRendering
{
    private function renderCategoryItem(int $index): string
    {
        $html = '';
        $pageData = $this->pages->data($index);
        if ($pageData['toxic_category']) {
            $html .= "<li class=\"toxic_category\">" . $pageData["toxic_category"] . "</li>\n";
        }
        return $html;
    }

    private function renderClasses(Request $request, int $page): string
    {
        $class = "";
        if (
            $page === $request->s()
            || ($this->conf["menu_sdoc"] === "parent" && $this->pages->isPageAncestorOf($page, $request->s()))
        ) {
            $class .= 's';
        }
        $class .= 'doc';
        if ($this->pages->children($page, (int) $this->conf["menu_levelcatch"])) {
            $class .= 's';
        }
        $pageData = $this->pages->data($page);
        if ($pageData['toxic_class']) {
            return $class . ' ' . $pageData['toxic_class'];
        } else {
            return $class;
        }
    }

    private function renderMenuItem(Request $request, int $page): string
    {
        $res = "";
        if ($page !== $request->s()) {
            $pageData = $this->pages->data($page);
            $res .= '<a href="' . $this->href($request, $page) . '"';
            if (!($request->admin() && $request->edit()) && $pageData["use_header_location"] === "2") {
                $res .= ' target="_blank"';
            }
            $res .= '>';
        } else {
            $res .= "<span>";
        }
        $res .= $this->pages->heading($page);
        if ($page !== $request->s()) {
            $res .= "</a>";
        } else {
            $res .= "</span>";
        }
        return $res;
    }

    private function href(Request $request, int $page): string
    {
        if ($page === $this->pages->firstPublished() && !($request->admin())) {
            return $request->url()->page("")->relative();
        } else {
            return $request->url()->page($this->pages->url($page))->relative();
        }
    }
}
