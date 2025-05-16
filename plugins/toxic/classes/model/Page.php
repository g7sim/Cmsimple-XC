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

final class Page
{
    /** @var ?int */
    private $index;

    /** @var ?Page */
    private $parent = null;

    /** @var ?Page */
    private $child = null;

    /** @var ?Page */
    private $prev = null;

    /** @var ?Page */
    private $next = null;

    /** @param list<int> $ta */
    public static function fromTocArray(array $ta, int $level, Pages $pages): ?self
    {
        if (empty($ta)) {
            return null;
        }
        $level--;
        $stack = [];
        foreach ($ta as $index) {
            if ($pages->level($index) === $level) {
                $page = self::stackTop($stack);
                $page = $page->addSibling($index);
            } elseif ($pages->level($index) < $level) {
                while ($pages->level($index) < $level--) {
                    $page = array_pop($stack);
                }
                $page = self::stackTop($stack);
                $page = $page->addSibling($index);
            } elseif ($pages->level($index) > $level) {
                while ($pages->level($index) > $level++) {
                    if (empty($stack)) {
                        $stack[] = $page = new self($pages->level($index) === $level ? $index : null);
                    } else {
                        $page = self::stackTop($stack);
                        $stack[] = $page = $page->addChild($pages->level($index) === $level ? $index : null);
                    }
                }
            }
            assert(isset($page));
            assert($page->index() !== null);
            $level = $pages->level($page->index());
        }
        assert(count($stack) > 0);
        $page = $stack[0];
        return $page;
    }

    /** @param list<self> $stack */
    private static function stackTop(array $stack): self
    {
        assert(!empty($stack));
        $page = end($stack);
        assert($page instanceof self);
        while ($page->next) {
            $page = $page->next;
        }
        return $page;
    }

    public function __construct(?int $index)
    {
        $this->index = $index;
    }

    public function addChild(?int $index): self
    {
        $that = new self($index);
        assert($this->child === null);
        $this->child = $that;
        $that->parent = $this;
        return $that;
    }

    public function addSibling(?int $index): self
    {
        $that = new self($index);
        assert($this->next === null);
        $this->next = $that;
        $that->prev = $this;
        return $that;
    }

    public function index(): ?int
    {
        return $this->index;
    }

    public function parent(): ?Page
    {
        return $this->parent;
    }

    public function child(): ?Page
    {
        return $this->child;
    }

    public function prev(): ?Page
    {
        return $this->prev;
    }

    public function next(): ?Page
    {
        return $this->next;
    }

    // breaks cycles to avoid GC
    public function release(): void
    {
        $this->parent = null;
        if ($this->child) {
            $this->child->release();
        }
        $this->prev = null;
        if ($this->next) {
            $this->next->release();
        }
    }

    /**
     * @param list<int> $acc
     * @return list<int>
     */
    public function toTocArray(array &$acc = []): array
    {
        $page = $this;
        do {
            if ($page->index !== null) {
                $acc[] = $page->index;
            }
            if ($page->child) {
                $page->child->toTocArray($acc);
            }
        } while ($page = $page->next);
        return $acc;
    }
}
