<?php

/**
 * Copyright 2021 Christoph M. Becker
 *
 * This file is part of Plib_XH.
 *
 * Plib_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plib_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plib_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Plib;

use function count;
use function http_build_query;
use function is_string;
use function parse_url;
use function preg_replace;

use const PHP_QUERY_RFC3986;
use const PHP_URL_PATH;

/**
 * An internal CMSimple_XH URL
 *
 * This class is not meant to be instantiated on its own;
 * instances should only be accessed via `Request::url()`.
 *
 * Since Plib_XH 1.4, the plugin generates
 * [clean URLs](https://cmsimpleforum.com/viewtopic.php?f=4&t=5719),
 * if that is enabled in the hidden configuration
 * (`plugin_cf['plib']['clean_urls']="true"`).
 */
final class Url
{
    /** @var string */
    private $base;

    /** @var string */
    private $page;

    /** @var array<string,mixed> */
    private $params;

    /**
     * @param array<string,mixed> $params
     * @internal
     */
    public function __construct(string $base, string $page, array $params)
    {
        $this->base = $base;
        $this->page = $page;
        $this->params = $params;
    }

    /**
     * Builds URL from a $pth element
     *
     * This allows to build, for instance, a URL for an image using
     * `$pth["folder"]["images"]` from the `Request::url()`.
     * Since page and params are usually meaningless in this context,
     * they are removed.
     *
     * @since 1.1
     */
    public function path(string $path): self
    {
        $that = clone $this;
        $path = implode("/", array_map("rawurlencode", explode("/", $path)));
        $that->base = (string) preg_replace(['/[^\/]*\/\.\.\//', '/\/\./'], '', $that->base . $path);
        $that->page = "";
        $that->params = [];
        return $that;
    }

    /**
     * Allows to refer to another page
     *
     * This is occassionally useful to provide a link to another page
     * of the Website.  Since it usually does not make sense to keep
     * existing query parameters in this case, they are removed.
     */
    public function page(string $page): self
    {
        $url = clone $this;
        $url->page = $page;
        $url->params = [];
        return $url;
    }

    public function with(string $name, string $value = ""): self
    {
        $url = clone $this;
        $url->params[$name] = $value;
        return $url;
    }

    public function without(string $name): self
    {
        $url = clone $this;
        unset($url->params[$name]);
        return $url;
    }

    public function absolute(): string
    {
        return $this->base . $this->suffix();
    }

    public function relative(): string
    {
        $relative = parse_url($this->base, PHP_URL_PATH);
        assert(is_string($relative));
        return $relative . $this->suffix();
    }

    private function suffix(): string
    {
        global $plugin_cf;

        $separator = ($plugin_cf["plib"]["clean_urls"] ?? "") ? "" : "?";
        $query = $this->query();
        if ($query !== "") {
            return $separator . $query;
        }
        return "";
    }

    private function query(): string
    {
        global $plugin_cf;

        $separator = ($plugin_cf["plib"]["clean_urls"] ?? "") ? "?" : "&";
        $query = $this->page;
        if (count($this->params) > 0) {
            $rest = http_build_query($this->params, "", "&", PHP_QUERY_RFC3986);
            $query .= $separator . preg_replace('/=(?=&|$)/', "", $rest);
        }
        return $query;
    }
}
