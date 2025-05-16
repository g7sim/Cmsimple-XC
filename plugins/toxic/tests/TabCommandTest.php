<?php

namespace Toxic;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class TabCommandTest extends TestCase
{
    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["toxic"];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $this->view = new View("./views/", $plugin_tx["toxic"]);
    }

    private function sut(): TabCommand
    {
        return new TabCommand($this->conf, $this->view);
    }

    public function testRendersClassInput(): void
    {
        $this->conf['classes_available'] = '';
        $request = new FakeRequest(["url" => "http://example.com/xh/?Welcome"]);
        $response = $this->sut()($request, ["toxic_class" => "test", "toxic_category" => ""]);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersClassSelect(): void
    {
        $this->conf['classes_available'] = 'one,two,three,test';
        $request = new FakeRequest(["url" => "http://example.com/xh/?Welcome"]);
        $response = $this->sut()($request, ["toxic_class" => "test", "toxic_category" => ""]);
        Approvals::verifyHtml($response->output());
    }
}
