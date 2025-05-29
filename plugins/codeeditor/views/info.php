<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $version
 * @var list<object{class:string,message:string}> $checks
 */
?>

<h1>Codeeditor <?=$this->esc($version)?></h1>
<h2><?=$this->text("syscheck_title")?></h2>
<?php foreach ($checks as $check):?>
<p class="<?=$this->esc($check->class)?>"><?=$this->esc($check->message)?></p>
<?php endforeach?>
