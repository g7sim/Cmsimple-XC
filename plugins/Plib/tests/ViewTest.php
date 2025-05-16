<?php

/**
 * Copyright (C) Christoph M. Becker
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
use org\bovigo\vfs\vfsStream;

class ViewTest extends TestCase
{
    public function testText(): void
    {
        $subject = new View("", ["text" => "%s %s"]);
        $actual = $subject->text("text", "this & that", "<p>world</p>");
        $this->assertSame("this &amp; that &lt;p&gt;world&lt;/p&gt;", $actual);
    }

    public function testPlain(): void
    {
        $subject = new View("", ["text" => "%s %s"]);
        $actual = $subject->plain("text", "this & that", "<p>world</p>");
        $this->assertSame("this & that <p>world</p>", $actual);
    }

    public function testRender(): void
    {
        vfsStream::setup("templates");
        file_put_contents(vfsStream::url("templates/test.php"), '<p><?=$foo?></p>');
        $subject = new View(vfsStream::url("templates/"), []);
        $actual = $subject->render("test", ["foo" => "bar"]);
        $this->assertSame("<p>bar</p>", $actual);
    }

    public function testRaw(): void
    {
        $sut = new View("", []);
        $this->assertEquals("<p>this & that</p>", $sut->raw("<p>this & that</p>"));
    }
}
