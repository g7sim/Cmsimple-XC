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
use Coco\Infra\XhStuff;
use Coco\Logic\Searcher;
use Coco\Logic\Util;
use Plib\CsrfProtector;
use Plib\Request;
use Plib\Response;
use Plib\View;

class Coco
{
    /** @var Repository */
    private $repository;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var XhStuff */
    private $xhStuff;

    /** @var View */
    private $view;

    public function __construct(
        Repository $repository,
        CsrfProtector $csrfProtector,
        XhStuff $xhStuff,
        View $view
    ) {
        $this->repository = $repository;
        $this->csrfProtector = $csrfProtector;
        $this->xhStuff = $xhStuff;
        $this->view = $view;
    }

    public function __invoke(Request $request, string $name, string $config, string $height): Response
    {
        if (!Util::isValidCocoName($name)) {
            return Response::create($this->view->message("fail", "error_invalid_name") . "\n");
        }
        if ($request->s() < 0) {
            return Response::create("");
        }
        if (!$request->admin() || !$request->edit()) {
            return $this->show($request, $name);
        }
        if ($request->post("coco_text_$name") === null) {
            return $this->edit($request, $name, $config, $height);
        }
        return $this->update($request, $name, $config, $height);
    }

    private function show(Request $request, string $name): Response
    {
        $content = $this->repository->find($name, $request->s());
        $content = $this->xhStuff->evaluateScripting($content);
        $search = $request->get("search") ?? "";
        if ($search !== "") {
            $words = Searcher::parseSearchTerm($search);
            $content = $this->xhStuff->highlightSearchWords($words, $content);
        }
        return Response::create($content);
    }

    private function edit(Request $request, string $name, string $config, string $height): Response
    {
        $content = $this->repository->find($name, $request->s());
        return Response::create($this->renderEditor($name, $config, $height, $content));
    }

    private function update(Request $request, string $name, string $config, string $height): Response
    {
        if (!$this->csrfProtector->check($request->post("xh_csrf_token"))) {
            return Response::create($this->view->message("fail", "error_unauthorized"));
        }
        $text = $request->post("coco_text_$name") ?? "";
        try {
            $this->repository->save($name, $request->s(), $text);
        } catch (RepositoryException $ex) {
            return Response::create(
                $this->view->message("fail", "error_save", $this->repository->filename($name))
                . $this->renderEditor($name, $config, $height, $text)
            );
        }
        return Response::redirect($request->url()->absolute());
    }

    private function renderEditor(string $name, string $config, string $height, string $content): string
    {
        $id = "coco_text_$name";
        $editor = $this->xhStuff->replaceEditor($id, $config);
        return $this->view->render("edit_form", [
            "id" => $id,
            "name" => $name,
            "style" => "width:100%; height:$height",
            "content" => $content,
            "editor" => $editor !== false ? $editor : false,
            "csrf_token" => $this->csrfProtector->token(),
        ]);
    }
}
