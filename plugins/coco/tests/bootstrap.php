<?php

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CMSIMPLE_URL = "http://example.com/";
const COCO_VERSION = "2.1";

require_once "../../cmsimple/functions.php";
require_once "../../cmsimple/utf8.php";
require_once "../../cmsimple/classes/CSRFProtection.php";
require_once "../../cmsimple/classes/PageDataRouter.php";
require_once "../../cmsimple/classes/Pages.php";

require_once "../plib/classes/CsrfProtector.php";
require_once "../plib/classes/Random.php";
require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

spl_autoload_register(function (string $className) {
    $parts = explode("\\", $className);
    if ($parts[0] !== "Coco") {
        return;
    }
    if (count($parts) === 3) {
        $parts[1] = strtolower($parts[1]);
    }
    $filename = implode("/", array_slice($parts, 1));
    if (is_readable("./classes/$filename.php")) {
        include_once "./classes/$filename.php";
    } elseif (is_readable("./tests/$filename.php")) {
        include_once "./tests/$filename.php";
    }
});
