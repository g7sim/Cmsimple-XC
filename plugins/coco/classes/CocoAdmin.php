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
use Coco\Infra\RepositoryException;
use Plib\CsrfProtector;
use Plib\Request;
use Plib\Response;
use Plib\View;

class CocoAdmin
{
    /** @var Repository */
    private $repository;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(Repository $repository, CsrfProtector $csrfProtector, View $view)
    {
        $this->repository = $repository;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($this->action($request)) {
            default:
                return $this->show();
            case "migrate":
                return $this->confirmMigrate($request);
            case "delete":
                return $this->confirmDelete($request);
            case "do_migrate":
                return $this->migrate($request);
            case "do_delete":
                return $this->delete($request);
        }
    }

    public function action(Request $request): string
    {
        $action = $request->get("action");
        if ($action && $action === $request->post("coco_do") && $request->getArray("coco_name") !== null) {
            return "do_$action";
        }
        if ($action === "migrate" && $request->getArray("coco_name") !== null) {
            return "migrate";
        }
        if ($action === "delete" && $request->getArray("coco_name") !== null) {
            return "delete";
        }
        return "";
    }

    private function show(): Response
    {
        $cocos = $this->repository->findAllNames();
        $oldCocos = array_diff($this->repository->findAllOldNames(), $cocos);
        return Response::create($this->view->render("admin", [
            "old_cocos" => $oldCocos,
            "cocos" => $cocos,
        ]))->withTitle("Coco – " . $this->view->text("menu_main"));
    }

    /** @param list<array{key:string,arg:string}> $errors */
    private function confirmMigrate(Request $request, array $errors = []): Response
    {
        return Response::create($this->view->render("confirm", [
            "errors" => $errors,
            "cocos" => $request->getArray("coco_name"),
            "csrf_token" => $this->csrfProtector->token(),
            "action" => "migrate",
        ]))->withTitle("Coco – " . $this->view->text("menu_main"));
    }

    /** @param list<array{key:string,arg:string}> $errors */
    private function confirmDelete(Request $request, array $errors = []): Response
    {
        return Response::create($this->view->render("confirm", [
            "errors" => $errors,
            "cocos" => $request->getArray("coco_name"),
            "csrf_token" => $this->csrfProtector->token(),
            "action" => "delete",
        ]))->withTitle("Coco – " . $this->view->text("menu_main"));
    }

    private function migrate(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("xh_csrf_token"))) {
            return Response::create($this->view->message("fail", "error_unauthorized"));
        }
        $errors = [];
        foreach (($request->getArray("coco_name") ?? []) as $name) {
            try {
                $this->repository->migrate($name);
            } catch (RepositoryException $ex) {
                $errors[] = ["key" => "error_migrate", "arg" => $this->repository->oldFilename($name)];
            }
        }
        if ($errors) {
            return $this->confirmMigrate($request, $errors);
        }
        return Response::redirect($request->url()->page("coco")->with("admin", "plugin_main")->absolute());
    }

    private function delete(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("xh_csrf_token"))) {
            return Response::create($this->view->message("fail", "error_unauthorized"));
        }
        $errors = [];
        foreach (($request->getArray("coco_name") ?? []) as $name) {
            foreach ($this->repository->findAllBackups($name) as $backup) {
                try {
                    $this->repository->delete(...$backup);
                } catch (RepositoryException $ex) {
                    $errors[] = ["key" => "error_delete", "arg" => $this->repository->filename(...$backup)];
                }
            }
            try {
                $this->repository->delete($name);
            } catch (RepositoryException $ex) {
                $errors[] = ["key" => "error_delete", "arg" => $this->repository->filename($name)];
            }
        }
        if ($errors) {
            return $this->confirmDelete($request, $errors);
        }
        return Response::redirect($request->url()->page("coco")->with("admin", "plugin_main")->absolute());
    }
}
