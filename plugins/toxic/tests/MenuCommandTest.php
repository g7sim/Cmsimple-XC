<?php

namespace Toxic;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Toxic\Model\Pages;
use XH\PageDataRouter;
use XH\Pages as XHPages;
use XH\Publisher;

class MenuCommandTest extends TestCase
{
    /** @var array<string,string> */
    private $conf;

    /** @var XHPages&Stub */
    private $pages;

    /** @var Publisher&Stub */
    private $publisher;

    /** @var PageDataRouter&Stub */
    private $pageData;

    public function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["toxic"];
        $this->conf["menu_levelcatch"] = "10";
        $this->conf["menu_levels"] = "3";
        $this->conf["menu_sdoc"] = "parent";
        $this->conf["uri_separator"] = ":";
        $this->pages = $this->createStub(XHPages::class);
        $this->publisher = $this->createStub(Publisher::class);
        $this->publisher->method("getFirstPublishedPage")->willReturn(1000);
        $this->publisher->method("isHidden")->willReturnCallback(function ($pageIndex) {
            return in_array($pageIndex, [4, 5]);
        });
        $this->setUpPageStructure();
        $this->pageData = $this->createStub(PageDataRouter::class);
        $this->pageData->method("find_page")->willReturnCallback(function ($pageIndex) {
            return [
                "toxic_class" => ($pageIndex >= 1 && $pageIndex <= 7) ? "blog" : "",
                "toxic_category" => ($pageIndex === 8) ? "About category" : "",
                "use_header_location" => ($pageIndex == 7) ? "2" : "0"
            ];
        });
    }

    private function setUpPageStructure(): void
    {
        $this->pages->method("heading")->willReturnMap([
            [0, 'Welcome'],
            [1, 'Blog'],
            [2, 'July'],
            [3, 'Hot'],
            [4, 'Hidden'],
            [5, 'AlsoHidden'],
            [6, 'January'],
            [7, 'Cold'],
            [8, 'About'],
            [9, 'Contact'],
            [10, 'News'],
        ]);
        $this->pages->method("url")->willReturnMap([
            [0, 'Welcome'],
            [1, 'Blog'],
            [2, 'Blog:July'],
            [3, 'Blog:July:Hot'],
            [4, 'Blog:Hidden'],
            [5, 'Blog:Hidden:AlsoHidden'],
            [6, 'Blog:January'],
            [7, 'Blog:January:Cold'],
            [8, 'About'],
            [9, 'About:Contact'],
            [10, 'News'],
        ]);
        $this->pages->method("level")->willReturnMap([
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
        ]);
        $this->pages->method("getAncestorsOf")->willReturnMap([
            [0, true, []],
            [1, true, []],
            [2, true, [1]],
            [3, true, [2, 1]],
            [4, true, [1]],
            [5, true, [4, 1]],
            [6, true, [1]],
            [7, true, [6, 1]],
            [8, true, []],
            [9, true, [8]],
            [10, true, []],
        ]);
        $this->pages->method("getCount")->willReturn(11);
    }

    private function sut(): MenuCommand
    {
        return new MenuCommand($this->conf, new Pages($this->pages, $this->publisher, $this->pageData));
    }

    public function testNoMenuItemsDisplayNothing(): void
    {
        $response = $this->sut()(new FakeRequest(), [], 1);
        $this->assertEmpty($response->output());
    }

    /** @dataProvider dataForUnorderedListlHasListItemChild */
    public function testUnorderedListHasListItemChild(string $class): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringMatchesFormat("%A<ul class=\"$class\">%w<li %A", $response->output());
    }

    public function dataForUnorderedListlHasListItemChild(): array
    {
        return [
            ['menulevel1'],
            ['menulevel2'],
            ['menulevel3'],
        ];
    }

    /** @dataProvider dataForListItemHasUnorderedListChild */
    public function testListItemHasUnorderedListChild(string $class): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringMatchesFormat("%A<li%A<ul class=\"$class\">%A", $response->output());
    }

    public function dataForListItemHasUnorderedListChild(): array
    {
        return [
            ['menulevel2'],
            ['menulevel3'],
        ];
    }

    public function testSelectedPageHasSpan(): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringContainsString("<span>Welcome</span>", $response->output());
    }

    public function testRendersMenu(): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        Approvals::verifyHtml($response->output());
    }

    public function testLiWithoutVisibleChilrenHasClassDoc(): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringContainsString(
            "<li class=\"doc blog\">\n        <a href=\"/?Blog:Hidden\">Hidden</a>",
            $response->output()
        );
    }

    /**
     * @param mixed $forOrFrom
     * @dataProvider dataForHasUlWithProperClass
     */
    public function testHasUlWithProperClass($forOrFrom, string $class): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), $forOrFrom);
        $this->assertStringContainsString("<ul class=\"$class\">", $response->output());
    }

    public function dataForHasUlWithProperClass(): array
    {
        return [
            [1, 'menulevel1'],
            [1, 'menulevel2'],
            [1, 'menulevel3'],
        ];
    }

    public function testSelectedPageHasClassSdocs(): void
    {
        $request = new FakeRequest(["s" => 1]);
        $response = $this->sut()($request, range(0, 10), 1);
        $this->assertStringContainsString("<li class=\"sdocs blog\">\n    <span>Blog</span>", $response->output());
    }

    public function testNotSelectedPageHasClassDocs(): void
    {
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringContainsString(
            "<li class=\"docs blog\">\n    <a href=\"/?Blog\">Blog</a>",
            $response->output()
        );
    }

    public function testNotSelectedChildlessPageHasClassDoc(): void
    {
        $request = new FakeRequest(["s" => 1]);
        $response = $this->sut()($request, range(0, 10), 1);
        Approvals::verifyHtml($response->output());
    }

    /** @dataProvider dataForParentOfSelectedPageHasClassDependingOnSdoc */
    public function testParentOfSelectedPageHasClassDependingOnSdoc(string $sdoc, string $class): void
    {
        $this->conf["menu_sdoc"] = $sdoc;
        $request = new FakeRequest(["s" => 2]);
        $response = $this->sut()($request, range(0, 10), 1);
        $this->assertStringContainsString(
            "<li class=\"$class blog\">\n    <a href=\"/?Blog\">Blog</a>",
            $response->output()
        );
    }

    public function dataForParentOfSelectedPageHasClassDependingOnSdoc(): array
    {
        return [
            ['parent', 'sdocs'],
            ['', 'docs'],
        ];
    }

    /** @dataProvider dataForH1WithH3HasClassDependingOnLevelcatch */
    public function testH1WithH3HasClassDependingOnLevelcatch(string $levelcatch, string $class): void
    {
        $this->conf["menu_levelcatch"] = $levelcatch;
        $response = $this->sut()(new FakeRequest(), range(0, 10), 1);
        $this->assertStringContainsString(
            "<li class=\"$class\">\n    <a href=\"/?About\">About</a>",
            $response->output()
        );
    }

    public function dataForH1WithH3HasClassDependingOnLevelcatch(): array
    {
        return [
            ['10', 'docs'],
            ['0', 'doc'],
        ];
    }

    public function testPageDoesntOpenInNewWindowInEditMode(): void
    {
        $request = new FakeRequest(["admin" => true, "edit" => true]);
        $response = $this->sut()($request, range(0, 10), 1);
        Approvals::verifyHtml($response->output());
    }
}
