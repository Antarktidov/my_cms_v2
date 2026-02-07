<?php
include __DIR__ . "/go_away.php";
global $wg_path;
?>
<form action="<?=$wg_path . "create_blog";?>" method="post">
    <input id="title" name="title">
    <input id="slug" name="slug">
    <textarea id="text" name="text"></textarea>
    <button type="submit">Сохранить</button>
</form>