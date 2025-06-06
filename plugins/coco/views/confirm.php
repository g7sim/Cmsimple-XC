<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.0 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var list<array{key:string,arg:string}> $errors
 * @var string $csrf_token
 * @var list<string> $cocos
 * @var string $action
 */
?>
<!-- coco confirmation -->
<h1>Coco – <?=$this->text('menu_main')?></h1>
<?foreach ($errors as $error):?>
<p class="xh_fail"><?=$this->text($error['key'], $error['arg'])?></p>
<?endforeach?>
<form method="post">
  <input type="hidden" name="xh_csrf_token" value="<?=$this->esc($csrf_token)?>">
  <p class="xh_warning"><?=$this->text("confirm_$action")?></p>
  <ul>
<?foreach ($cocos as $coco):?>
    <li><?=$this->esc($coco)?></li>
<?endforeach?>
  </ul>
  <p><button name="coco_do" value="<?=$this->esc($action)?>"><?=$this->text("label_$action")?></button></p>
</form>
