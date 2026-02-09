<?php
include __DIR__ . "/../../go_away.php";
?>
<form class="contact-form" action="#" method="post">
    <h1>Оставьте своё сообщение для нас</h1>
    <?=get_csrf_form_field();?>
    <textarea name="message" id="message"></textarea>
    <button type="submit">Отправить</button>
</form>