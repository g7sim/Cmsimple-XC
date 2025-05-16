<?php

namespace Toxic;

use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;
use XH\Publisher;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $c, $xh_publisher, $pd_router, $pth, $cf, $plugin_cf, $tx, $plugin_tx;
        $c = [];
        $xh_publisher = $this->createStub(Publisher::class);
        $pd_router = $this->createStub(PageDataRouter::class);
        $pth = ["folder" => ["plugins" => ""]];
        $cf = ["menu" => ["levelcatch" => "", "levels" => "", "sdoc" => ""], "uri" => ["seperator" => ""]];
        $plugin_cf = ["toxic" => []];
        $tx = ["submenu" => ["heading" => ""]];
        $plugin_tx = ["toxic" => []];
    }

    public function testMakesMenuCommand(): void
    {
        $this->assertInstanceOf(MenuCommand::class, Dic::menuCommand([], 1));
    }

    public function testMakesSubmenuCommand(): void
    {
        $this->assertInstanceOf(SubmenuCommand::class, Dic::submenuCommand());
    }

    public function testMakeTabCommand(): void
    {
        $this->assertInstanceOf(TabCommand::class, Dic::makeTabCommand([]));
    }

    public function testMakeInfoCommand(): void
    {
        $this->assertInstanceOf(InfoCommand::class, Dic::makeInfoCommand());
    }
}
