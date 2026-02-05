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
    
    // Убираем параметры запроса и базовый путь если нужно
    $parsed_url = parse_url($request_uri);
    $path = $parsed_url['path'] ?? '';
    
    // Убираем начальный и конечный слеши
    $path = trim($path, '/');
    
    // Убираем базовый путь (если скрипт находится в поддиректории)
    $script_path = dirname($_SERVER['SCRIPT_NAME']);
    if ($script_path !== '/' && strpos($path, $script_path) === 0) {
        $path = substr($path, strlen(trim($script_path, '/')));
        $path = trim($path, '/');
    }
    
    // Разбиваем путь на части
    $path_parts = $path ? explode('/', $path) : [];
    
    // Простой роутинг
    if (empty($path_parts) || $path_parts[0] === 'home') {
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
    
    // Здесь вы можете получить данные блога из базы данных по slug
    echo "Вы просматриваете блог: " . $slug;
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
    global $my_cms_skin;
    $output = "<link rel=\"stylesheet\" href=\"./skins/{$my_cms_skin}/css/skin.css\">";
    return $output;
}
function get_heder_scripts() {
    global $my_cms_skin;
    $output = "<script src=\"./skins/{$my_cms_skin}/js/skin.js\" defer></script>";
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