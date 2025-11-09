<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $version
 * @var list<array{class:string,key:string,arg:string,state:string}> $checks
 */
?>
<!-- privacy plugin info -->
<h1>Privacy <?=$this->esc($version)?></h1>
<h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
<p class="<?=$this->esc($check['class'])?>"><?=$this->text($check['key'], $check['arg'])?><?=$this->text($check['state'])?></p>
<?php endforeach?>
