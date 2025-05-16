<?php

namespace Plib;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function setUp(): void
    {
        global $su, $plugin_cf;

        $su = "";
        $plugin_cf = ["plib" => ["clean_urls" => ""]];
    }

    public function testUrl(): void
    {
        $this->assertEquals("http://example.com/", Request::current()->url()->absolute());
    }

    /** @dataProvider getData */
    public function testGet(string $name, $value, ?string $expected): void
    {
        $_GET = [$name => $value];
        $sut = Request::current();
        $this->assertEquals($expected, $sut->get($name));
    }

    public function getData(): array
    {
        return [
            ["foo", "bar", "bar"],
            ["foo", null, null],
            ["foo", [], null],
        ];
    }

    /** @dataProvider postData */
    public function testPost(string $name, $value, ?string $expected): void
    {
        $_POST = [$name => $value];
        $sut = Request::current();
        $this->assertEquals($expected, $sut->post($name));
    }

    public function postData(): array
    {
        return [
            ["foo", "bar", "bar"],
            ["foo", null, null],
            ["foo", [], null],
        ];
    }

    /** @dataProvider cookieData */
    public function testCookie(string $name, $value, ?string $expected): void
    {
        $_COOKIE = [$name => $value];
        $sut = Request::current();
        $this->assertEquals($expected, $sut->cookie($name));
    }

    public function cookieData(): array
    {
        return [
            ["foo", "bar", "bar"],
            ["foo", null, null],
            ["foo", [], null],
        ];
    }

    public function testTime(): void
    {
        $_SERVER["REQUEST_TIME"] = 12345;
        $this->assertEquals(12345, Request::current()->time());
    }

    public function testAdmin(): void
    {
        $this->assertFalse(Request::current()->admin());
    }

    public function testLanguage(): void
    {
        global $sl;

        $sl = "de";
        $this->assertEquals("de", Request::current()->language());
    }
}
