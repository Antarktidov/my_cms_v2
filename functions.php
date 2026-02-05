<?php
include __DIR__ . "/go_away.php";
function my_cms_header() {
    ?>
    <!DOCTYPE html>
    <html lang="<?=get_locale();?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?=get_html_title();?></title>
        <?php
        echo get_styles();
        echo get_heder_scripts();
        ?>
    </head>
    <body>
    <?php
}
function my_cms_footer() {
    ?>
    </body>
</html>
    <?php
}
function get_locale() {
    return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}
function get_html_title() {
    return 'my_cms';
}
function get_styles() {
    return '';
}
function get_heder_scripts() {
    return '';
}
function load_skin() {
    global $my_cms_skin;
    include __DIR__ . "/skins/{$my_cms_skin}/skin.php";
}
function connect_to_db() {
    global $db_host, $db_username, $db_password, $db_name, $conn;
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}
function close_connection_to_db() {
    global $conn;
    $conn->close();
}