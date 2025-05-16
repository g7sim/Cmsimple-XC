<?php

namespace Toxic\Model;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;
use XH\Pages as XHPages;
use XH\Publisher;

/** @small */
class PageTest extends TestCase
{
    /** @var XHPages&Stub */
    private $pages;

    /** @var Publisher&Stub */
    private $publisher;

    /** @var PageDataRouter&Stub */
    private $pageData;

    public function setUp(): void
    {
        $this->pages = $this->createStub(XHPages::class);
        $this->publisher = $this->createStub(Publisher::class);
        $this->pageData = $this->createStub(PageDataRouter::class);
    }

    private function pages(): Pages
    {
        return new Pages($this->pages, $this->publisher, $this->pageData);
    }

    /** @dataProvider roundTripData */
    public function testTocArrayRoundTrip(array $levelMap): void
    {
        $this->pages->method("level")->willReturnMap($levelMap);
        $ta = range(0, count($levelMap) - 1);
        $page = Page::fromTocArray($ta, 1, $this->pages());
        $this->assertEquals($ta, $page->toTocArray());
    }

    public function roundTripData(): array
    {
        return [
            [[
                [0, 1],
                [1, 1],
                [2, 2],
                [3, 3],
                [4, 2],
                [5, 3],
                [6, 2],
                [7, 3],
                [8, 1],
                [9, 3],
                [10, 1],
            ]],
            [[
                [0, 1],
                [1, 3],
            ]],
        ];
    }
}
