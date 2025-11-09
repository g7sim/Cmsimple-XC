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
<h1>Coco – <?php echo $this->text('menu_main'); ?></h1>

<?php foreach ($errors as $error): ?>
  <p class="xh_fail">
    <?php echo $this->text($error['key'], $error['arg']); ?>
  </p>
<?php endforeach; ?>

<form method="post">
  <input type="hidden" name="xh_csrf_token" value="<?php echo $this->esc($csrf_token); ?>">
  <p class="xh_warning">
    <?php echo $this->text("confirm_$action"); ?>
  </p>
  <ul>
    <?php foreach ($cocos as $coco): ?>
      <li><?php echo $this->esc($coco); ?></li>
    <?php endforeach; ?>
  </ul>
  <p>
    <button name="coco_do" value="<?php echo $this->esc($action); ?>">
      <?php echo $this->text("label_$action"); ?>
    </button>
  </p>
</form>
