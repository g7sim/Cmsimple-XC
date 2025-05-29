<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<string> $stylesheets
 * @var string $codemirror
 * @var string $codeeditor
 * @var string $text
 * @var string $filebrowser
 */
?>

<meta name="xh-codeeditor" content='<?=$this->json($text)?>'>
<?php foreach ($stylesheets as $stylesheet):?>
<link rel="stylesheet" type="text/css" href="<?=$this->esc($stylesheet)?>">
<?php endforeach?>
<script src="<?=$this->esc($codemirror)?>"></script>
<script src="<?=$this->esc($codeeditor)?>"></script>
<script>
<?=$this->raw($filebrowser)?>
</script>
