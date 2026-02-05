<?php
include __DIR__ . "/go_away.php";

$my_cms_skin = "bootstrap";
load_extension("comments");

$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "my_cms";

$wg_path = "http://localhost/my_cms/";

function load_extension($ext) {
    include __DIR__ . "/extensions/{$ext}/functions.php";
}