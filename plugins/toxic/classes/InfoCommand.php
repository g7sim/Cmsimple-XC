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
        return Response::create("<h1>Toxic " . Dic::VERSION . "</h1>\n"
            . "<h2>" . $this->view->text("syscheck_heading") . "</h2>\n"
            . $this->systemChecks());
    }

    private function systemChecks(): string
    {
        $checks = [];
        $version = "7.1.0";
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version);
        $checks[] = $this->view->message(
            $state ? "success" : "fail",
            "syscheck_phpversion",
            $version,
            $this->view->plain("syscheck_" . ($state ? "good" : "bad"))
        );
        $version = "1.7.0";
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version");
        $checks[] = $this->view->message(
            $state ? "success" : "fail",
            "syscheck_xhversion",
            $version,
            $this->view->plain("syscheck_" . ($state ? "good" : "bad"))
        );
        $version = "1.8";
        $state = $this->systemChecker->checkPlugin("plib", $version);
        $checks[] = $this->view->message(
            $state ? "success" : "fail",
            "syscheck_plibversion",
            $version,
            $this->view->plain("syscheck_" . ($state ? "good" : "bad"))
        );
        foreach (["config", "css", "languages"] as $folder) {
            $folder = $this->pluginFolder . $folder;
            $state = $this->systemChecker->checkWritability($folder);
            $checks[] = $this->view->message(
                $state ? "success" : "warning",
                "syscheck_writable",
                $folder,
                $this->view->plain("syscheck_" . ($state ? "good" : "bad"))
            );
        }
        return implode("", $checks);
    }
}
