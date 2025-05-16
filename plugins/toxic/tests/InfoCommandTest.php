<?php

namespace Toxic;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoCommandTest extends TestCase
{
    /** @var FakeSystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->systemChecker = new FakeSystemChecker();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["toxic"]);
    }

    private function sut(): InfoCommand
    {
        return new InfoCommand(
            "./plugins/toxic/",
            $this->systemChecker,
            $this->view
        );
    }

    public function testRendersPluginInfo(): void
    {
        $response = $this->sut()();
        Approvals::verifyHtml($response->output());
    }
}
