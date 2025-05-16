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

class TabCommand
{
    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, View $view)
    {
        $this->conf = $conf;
        $this->view = $view;
    }

    /** @param array<string,string> $pageData */
    public function __invoke(Request $request, array $pageData): Response
    {
        return Response::create($this->view->render("pdtab", [
            "url" => $request->url()->relative(),
            "category" => $pageData['toxic_category'],
            "has_classes" => trim($this->conf["classes_available"]) !== "",
            "available_classes" => $this->availableClasses($pageData),
            "toxic_class" => $pageData['toxic_class'],
        ]));
    }

    /**
     * @param array<string,string> $pageData
     * @return list<object{value:string,label:string,selected:string}>
     */
    private function availableClasses(array $pageData): array
    {
        $classes = $this->conf["classes_available"];
        $classes = array_map("trim", explode(",", $classes));
        array_unshift($classes, "");
        $res = [];
        foreach ($classes as $class) {
            $res[] = (object) [
                "value" => $class,
                "label" => $class === "" ? $this->view->plain("label_none") : $class,
                "selected" => $class === $pageData["toxic_class"] ? "selected" : "",
            ];
        }
        return $res;
    }
}
