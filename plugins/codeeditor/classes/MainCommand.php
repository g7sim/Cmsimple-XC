<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Codeeditor_XH.
 *
 * Codeeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Codeeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codeeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Codeeditor;

use Plib\Request;
use Plib\Response;

class MainCommand
{
    /** @var Editor */
    private $editor;

    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
    }

    public function __invoke(Request $request): Response
    {
        if ($this->isEditingPhp($request)) {
            $mode = 'php';
            $class = 'xh_file_edit';
        } elseif ($this->isEditingCss($request)) {
            $mode = 'css';
            $class = 'xh_file_edit';
        } else {
            return Response::create();
        }
        return $this->editor->init($request, [$class], '', $mode, false);
    }

    private function isEditingPhp(Request $request): bool
    {
        $action = $request->get("action") ?? "";
        $file = $request->get("file");
        return $file == 'template' && ($action == 'edit' || $action == '')
            || $file == 'content' && ($action == 'edit' || $action == '');
    }

    private function isEditingCss(Request $request): bool
    {
        $action = $request->get("action") ?? "";
        $admin = $request->get("admin");
        $file = $request->get("file");
        return $file == 'stylesheet' && ($action == 'edit' || $action == '')
            || $admin == 'plugin_stylesheet' && $action == 'plugin_text';
    }
}
