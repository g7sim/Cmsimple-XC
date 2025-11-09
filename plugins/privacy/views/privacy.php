<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string|null $message
 */
?>
<!-- privacy form -->
<div id="privacy">
  <form name="privacy" method="post">
<?php if (isset($message)):?>
    <div><?=$this->raw($message)?></div>
<?php else:?>
    <p><?=$this->text('message')?></p>
<?php endif?>
    <p class="privacy_buttons">
      <button type="submit" class="submit" name="privacy_agree" value="no"><?=$this->text('label_decline')?></button>
      <button type="submit" class="submit" name="privacy_agree" value="yes"><?=$this->text('label_allow')?></button>
    </p>
  </form>
</div>
<script>
if (typeof navigator.cookieEnabled != "undefined" && !navigator.cookieEnabled) {
    document.forms.privacy.style.display = "none";
}
</script>
