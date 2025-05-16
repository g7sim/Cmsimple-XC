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

use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function setUp(): void
    {
        global $plugin_cf;

        $plugin_cf = ["plib" => ["clean_urls" => ""]];
    }

    public function testAbsolute(): void
    {
        $url = new Url("http://example.com/", "", ["a" => "b"]);
        $url = $url->page("foo")->without("a")->with("bar", "baz");
        $this->assertSame("http://example.com/?foo&bar=baz", $url->absolute());
    }

    public function testRelative(): void
    {
        $url = new Url("http://example.com/", "", []);
        $url = $url->page("foo")->with("bar", "baz");
        $this->assertSame("/?foo&bar=baz", $url->relative());
    }

    public function testWithoutPage(): void
    {
        $url = new Url("http://example.com/", "", []);
        $this->assertSame("/", $url->relative());
    }

    public function testCyrillicSubPage(): void
    {
        $url = new Url("http://example.com/", "Делаем/Печи", []);
        $this->assertEquals("/?Делаем/Печи", $url->relative());
    }

    public function testPathForPrimaryLanguage(): void
    {
        $url = new Url("http://example.com/", "Page", ["foo" => "bar"]);
        $url = $url->path("./userfiles/images/baz.jpg");
        $this->assertSame("http://example.com/userfiles/images/baz.jpg", $url->absolute());
        $this->assertSame("/userfiles/images/baz.jpg", $url->relative());
    }

    public function testPathForSecondaryLanguage(): void
    {
        $url = new Url("http://example.com/de/", "Page", ["foo" => "bar"]);
        $url = $url->path("../userfiles/images/baz.jpg");
        $this->assertSame("http://example.com/userfiles/images/baz.jpg", $url->absolute());
        $this->assertSame("/userfiles/images/baz.jpg", $url->relative());
    }

    public function testCleanUrls(): void
    {
        global $plugin_cf;

        $plugin_cf["plib"]["clean_urls"] = "true";
        $url = new Url("http://example.com/", "", ["a" => "b"]);
        $url = $url->page("foo")->without("a")->with("bar", "baz")->with("c", "d");
        $this->assertSame("http://example.com/foo?bar=baz&c=d", $url->absolute());
        $this->assertSame("/foo?bar=baz&c=d", $url->relative());
    }
}
