<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $url
 */
?>
<!-- privacy link -->
<p><a href="<?=$this->esc($url)?>"><?=$this->text('label_link')?></a></p>
