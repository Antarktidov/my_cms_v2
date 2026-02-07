<?php
include __DIR__ . "/go_away.php";
global $wg_path;
?>
<form action="<?=$wg_path . "create_blog";?>" method="post">
    <h1 style="margin-bottom: 0;">Создать блог</h1>
    <label for="title">Название</label>
    <input id="title" name="title">
    <label for="slug">url-название</label>
    <input id="slug" name="slug">
    <label for="text">Текст</label>
    <textarea id="text" name="text"></textarea>
    <button type="submit">Сохранить</button>
</form>