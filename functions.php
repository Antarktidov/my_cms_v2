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
function my_cms_content() {
    $request_uri = $_SERVER['REQUEST_URI'];

    // Убираем параметры запроса
    $parsed_url = parse_url($request_uri);
    $path = $parsed_url['path'] ?? '';

    // Убираем начальный и конечный слеши
    $path = trim($path, '/');

    // Учитываем, что сайт может находиться в поддиректории (например /my_cms)
    $script_dir = trim(dirname($_SERVER['SCRIPT_NAME']), '/'); // my_cms
    if ($script_dir !== '' && strpos($path, $script_dir) === 0) {
        // Отрезаем базовую директорию из пути
        $path = substr($path, strlen($script_dir));
        $path = ltrim($path, '/');
    }

    // Разбиваем путь на части
    $path_parts = $path ? explode('/', $path) : [];

    // Простой роутинг
    if (empty($path_parts) || $path_parts[0] === '' || $path_parts[0] === 'home' || $path_parts[0] === 'index.php') {
        my_cms_homepage();
    } elseif ($path_parts[0] === 'blog') {
        if (isset($path_parts[1])) {
            $blog_slug = $path_parts[1];
            my_cms_single_blog($blog_slug);
        } else {
            my_cms_blog_page();
        }
    } else {
        http_response_code(404);
        global $my_cms_skin;
        include __DIR__ . "/skins/{$my_cms_skin}/404.php";
    }
}
// Новая функция для отображения конкретного блога
function my_cms_single_blog($slug) {
    // Очищаем slug от нежелательных символов
    $slug = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
    
    connect_to_db();
    global $conn;
    $sql = "SELECT title, text FROM blog WHERE slug='$slug'";
    $result = $conn->query($sql);

    // Process the result set
    if ($result->num_rows === 1) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        ?>
        <h1><?=$row['title'];?></h1>
        <?=$row['text'];?>
        <?php
    }
    } else if ($result->num_rows === 0) {
        http_response_code(404);
        global $my_cms_skin;
        include __DIR__ . "/skins/{$my_cms_skin}/404.php";
    }

    close_connection_to_db();
}
function my_cms_homepage() {
    ?>
    <h1>Домашняя страница</h1>
    <p>Это домашняя страница MY_CMS</p>
    <?php
}
function my_cms_blog_page() {
    echo "Заглушка блога";
}
function get_locale() {
    return Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
function get_html_title() {
    return 'my_cms';
}
function get_styles() {
    global $my_cms_skin, $wg_path;
    $output = "<link rel=\"stylesheet\" href=\"{$wg_path}/skins/{$my_cms_skin}/css/skin.css\">";
    return $output;
}
function get_heder_scripts() {
    global $my_cms_skin, $wg_path;
    $output = "<script src=\"{$wg_path}/skins/{$my_cms_skin}/js/skin.js\" defer></script>";
    return $output;
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