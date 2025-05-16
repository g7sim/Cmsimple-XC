<?php

namespace Plib;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class SystemCheckerTest extends TestCase
{
    private SystemChecker $sut;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->sut = new SystemChecker();
    }

    /** @dataProvider versionData */
    public function testCheckVersion(string $actual, string $minimum, bool $expected): void
    {
        $this->assertEquals($expected, $this->sut->checkVersion($actual, $minimum));
    }

    public function versionData(): array
    {
        return [
            ["7.4.0", "7.4.0", true],
            ["8.0.0", "7.4.0", true],
            ["7.3.99", "7.4.0", false],
        ];
    }

    public function testCheckExtension(): void
    {
        $this->assertTrue($this->sut->checkExtension("standard"));
        $this->assertFalse($this->sut->checkExtension("not existing extension"));
    }

    public function testCheckWritability(): void
    {
        $this->assertTrue($this->sut->checkWritability(vfsStream::url("root")));
    }
}
