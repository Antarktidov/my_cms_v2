<?php
include __DIR__ . "/../../go_away.php";
global $wg_path;
?>
<form class="comment-form" action="#" method="post">
    <input type="number" id="blog_id" name="blog_id" hidden value="<?=$blog_id?>">
    <textarea id="comment" placeholder="Введите комментарий..."></textarea>
    <button type="submit">Опубликовать</button>
</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    post_blog_comment();
}