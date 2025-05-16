<?php

namespace Toxic;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use Toxic\Model\Pages;
use XH\PageDataRouter;
use XH\Pages as XHPages;
use XH\Publisher;

class SubmenuCommandTest extends TestCase
{
    /** @var array<string,string> */
    private $conf;

    /** @var XHPages&Stub */
    private $pages;

    /** @var View */
    private $view;

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
        $this->pages = $this->createStub(XHPages::class);
        $this->setUpPageStructure();
        $lang = XH_includeVar("./languages/en.php", "plugin_tx")["toxic"];
        $lang["submenu_heading"] = "Submenu";
        $this->view = new View("./views/", $lang);
        $this->publisher = $this->createStub(Publisher::class);
        $this->publisher->method("getFirstPublishedPage")->willReturn(1000);
        $this->publisher->method("isHidden")->willReturnCallback(function ($pageIndex) {
            return in_array($pageIndex, [4, 5]);
        });
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

    private function sut(): SubmenuCommand
    {
        return new SubmenuCommand(
            $this->conf,
            new Pages($this->pages, $this->publisher, $this->pageData),
            $this->view
        );
    }

    public function testDoesNotRenderEmptySubmenu(): void
    {
        $request = new FakeRequest(["s" => 0]);
        $response = $this->sut()($request, "");
        $this->assertSame("", $response->output());
    }

    public function testRendersSubmenu(): void
    {
        $request = new FakeRequest(["s" => 1]);
        $response = $this->sut()($request, "");
        Approvals::verifyHtml($response->output());
    }

    public function testRendersCustomSubmenuHeading(): void
    {
        $request = new FakeRequest(["s" => 1]);
        $response = $this->sut()($request, "<h4 class=\"submenu_heading\">%s</h4>");
        $this->assertStringContainsString("<h4 class=\"submenu_heading\">Submenu</h4>", $response->output());
    }
}
