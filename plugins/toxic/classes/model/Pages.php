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

namespace Toxic\Model;

use XH\PageDataRouter;
use XH\Pages as XHPages;
use XH\Publisher;

class Pages
{
    /** @var XHPages */
    private $pages;

    /** @var Publisher */
    private $publisher;

    /** @var PageDataRouter */
    private $pageData;

    public function __construct(XHPages $pages, Publisher $publisher, PageDataRouter $pageData)
    {
        $this->pages = $pages;
        $this->publisher = $publisher;
        $this->pageData = $pageData;
    }

    public function count(): int
    {
        return $this->pages->getCount();
    }

    public function firstPublished(): int
    {
        return $this->publisher->getFirstPublishedPage();
    }

    /** @return array<string,string> */
    public function data(int $page): array
    {
        return $this->pageData->find_page($page);
    }

    public function level(int $page): int
    {
        return $this->pages->level($page);
    }

    public function heading(int $page): string
    {
        return $this->pages->heading($page);
    }

    public function url(int $page): string
    {
        return $this->pages->url($page);
    }

    public function hidden(int $page): bool
    {
        return $this->publisher->isHidden($page);
    }

    /** @return list<int> */
    public function children(int $page, int $levelCatch): array
    {
        $result = [];
        $ll = $levelCatch;
        for ($i = $page + 1; $i < $this->count(); ++$i) {
            if ($this->level($i) <= $this->level($page)) {
                break;
            }
            if ($this->level($i) <= $ll && !$this->hidden($i)) {
                $result[] = $i;
            }
            if ($this->level($i) < $ll) {
                $ll = $this->level($i);
            }
        }
        return $result;
    }

    public function isPageAncestorOf(int $page, int $other): bool
    {
        return in_array($page, $this->pages->getAncestorsOf($other), true);
    }
}
