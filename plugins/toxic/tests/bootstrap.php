<?php

require_once "./vendor/autoload.php";

require_once "../../cmsimple/classes/PageDataRouter.php";
require_once "../../cmsimple/classes/Pages.php";
require_once "../../cmsimple/classes/Publisher.php";
require_once "../../cmsimple/functions.php";
require_once "../../cmsimple/compat.php";

require_once "../plib/classes/Request.php";
require_once "../plib/classes/Response.php";
require_once "../plib/classes/Url.php";
require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeRequest.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/model/Page.php";
require_once "./classes/model/Pages.php";
require_once "./classes/Dic.php";
require_once "./classes/ListRendering.php";
require_once "./classes/InfoCommand.php";
require_once "./classes/MenuCommand.php";
require_once "./classes/SubmenuCommand.php";
require_once "./classes/TabCommand.php";

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
