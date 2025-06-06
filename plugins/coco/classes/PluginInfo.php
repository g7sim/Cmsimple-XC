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

namespace Coco;

use Coco\Infra\Repository;
use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class PluginInfo
{
    /** @var string */
    private $pluginFolder;

    /** @var Repository */
    private $repository;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        Repository $repository,
        SystemChecker $systemChecker,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->repository = $repository;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        return Response::create($this->view->render("info", [
            "version" => COCO_VERSION,
            "checks" => $this->getChecks(),
        ]))->withTitle("Coco " . COCO_VERSION);
    }

    /**
     * @return list<object{class:string,message:string}>
     */
    public function getChecks(): array
    {
        return [
            $this->checkPhpVersion("7.1.0"),
            $this->checkXhVersion("1.7.0"),
            $this->checkPlibVersion("1.6"),
            $this->checkWritability($this->pluginFolder . "css/"),
            $this->checkWritability($this->pluginFolder . "languages/"),
            $this->checkWritability($this->repository->dataFolder())
        ];
    }

    /** @return object{class:string,message:string} */
    private function checkPhpVersion(string $version): object
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version);
        return (object) [
            "class" => $state ? "xh_success" : "xh_fail",
            "message" => $this->view->plain($state ? "syscheck_phpversion" : "syscheck_phpversion_no", $version),
        ];
    }

    /** @return object{class:string,message:string} */
    private function checkXhVersion(string $version): object
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version");
        return (object) [
            "class" => $state ? "xh_success" : "xh_fail",
            "message" => $this->view->plain($state ? "syscheck_xhversion" : "syscheck_xhversion_no", $version),
        ];
    }

    /** @return object{class:string,message:string} */
    private function checkPlibVersion(string $version): object
    {
        $state = $this->systemChecker->checkPlugin("plib", $version);
        return (object) [
            "class" => $state ? "xh_success" : "xh_fail",
            "message" => $this->view->plain($state ? "syscheck_plibversion" : "syscheck_plibversion_no", $version),
        ];
    }

    /** @return object{class:string,message:string} */
    private function checkWritability(string $folder): object
    {
        $state = $this->systemChecker->checkWritability($folder);
        return (object) [
            "class" => $state ? "xh_success" : "xh_warning",
            "message" => $this->view->plain($state ? "syscheck_writable" : "syscheck_writable_no", $folder),
        ];
    }
}
