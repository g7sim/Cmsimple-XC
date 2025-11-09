<?php

use Plib\Request;
use Privacy\Dic;

if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

/**
 * @var string $admin
 * @var string $o
 */

XH_registerStandardPluginMenuItems(false);

if (XH_wantsPluginAdministration("privacy")) {
    $o .= print_plugin_admin("off");
    switch ($admin) {
        case "":
            $o .= Dic::makePluginInfo()(Request::current())();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
