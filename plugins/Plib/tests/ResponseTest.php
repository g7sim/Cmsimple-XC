<?php

namespace Plib;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testNormalResponse(): void
    {
        $response = Response::create("some output");
        $this->assertEquals("some output", $response->output());
        $this->assertEquals("some output", $response());
    }

    public function testTitle(): void
    {
        global $title;

        $response = Response::create()->withTitle("a title");
        $this->assertEquals("a title", $response->title());
        $response();
        $this->assertEquals("a title", $title);
    }

    public function testCookie(): void
    {
        $response = Response::create()->withCookie("foo", "bar", 0);
        $this->assertEquals(["foo", "bar", 0], $response->cookie());
    }

    public function testRedirect(): void
    {
        $response = Response::redirect("http://example.com/see_other");
        $this->assertEquals("http://example.com/see_other", $response->location());
    }
}
