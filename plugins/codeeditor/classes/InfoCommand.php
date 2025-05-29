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

use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class InfoCommand
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        return Response::create($this->view->render("info", [
            "version" => CODEEDITOR_VERSION,
            "checks" => $this->systemChecks(),
        ]))->withTitle($this->view->esc("Codeeditor " . CODEEDITOR_VERSION));
    }

    /** @return list<object{class:string,message:string}> */
    private function systemChecks(): array
    {
        $checks = [];
        $phpVersion = "7.1.0";
        $checks[] = (object) [
            "class" => $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion)
                ? "xh_success"
                : "xh_fail",
            "message" => $this->view->plain("syscheck_phpversion", $phpVersion),
        ];
        $xhVersion = "1.7.0";
        $checks[] = (object) [
            "class" => $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion")
                ? "xh_success"
                : "xh_fail",
            "message" => $this->view->plain("syscheck_xhversion", $xhVersion),
        ];
        $plibVersion = "1.3";
        $checks[] = (object) [
            "class" => $this->systemChecker->checkPlugin("plib", $plibVersion)
                ? "xh_success"
                : "xh_fail",
            "message" => $this->view->plain("syscheck_plibversion", $plibVersion),
        ];
        foreach (array("config/", "css/", "languages/") as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        foreach ($folders as $folder) {
            $checks[] = (object) [
                "class" =>  $this->systemChecker->checkWritability($folder)
                    ? "xh_success"
                    : "xh_warning",
                "message" => $this->view->plain("syscheck_writable", $folder),
            ];
        }
        return $checks;
    }
}
