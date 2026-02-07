<?php
include __DIR__ . "/go_away.php";

/**
 * Система хуков — позволяет расширениям подключаться к точкам вызова в ядре
 */
$my_cms_hooks = [];

function my_cms_add_hook($hook_name, $callback, $priority = 10) {
    global $my_cms_hooks;
    if (!isset($my_cms_hooks[$hook_name])) {
        $my_cms_hooks[$hook_name] = [];
    }
    $my_cms_hooks[$hook_name][] = ['callback' => $callback, 'priority' => (int) $priority];
}

function my_cms_do_hook($hook_name, $data = null) {
    global $my_cms_hooks;
    if (!isset($my_cms_hooks[$hook_name]) || empty($my_cms_hooks[$hook_name])) {
        return;
    }
    $hooks = $my_cms_hooks[$hook_name];
    usort($hooks, fn($a, $b) => $a['priority'] <=> $b['priority']);
    foreach ($hooks as $hook) {
        call_user_func($hook['callback'], $data);
    }
}

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
    } elseif ($path_parts[0] === 'create_blog') {
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            include __DIR__  . '/create-blog.php';
        } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
            create_blog_page();
        } 
    } else {
        my_cms_error_404();
    }
}
// Новая функция для отображения конкретного блога
function my_cms_single_blog($slug) {
    // Очищаем slug от нежелательных символов
    $slug = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
    
    connect_to_db();
    global $conn;
    
    // Подготовка запроса для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT id, title, text FROM blog WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    // Process the result set
    if ($result->num_rows === 1) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        ?>
        <h1><?=htmlspecialchars($row['title']);?></h1>
        <?=htmlspecialchars($row['text']);?>
        <?php
        my_cms_do_hook('single_blog_after_content', $row);
    }
    } else if ($result->num_rows === 0) {
        my_cms_error_404();
    }
    
    // Закрытие подготовленного запроса
    $stmt->close();
    
    close_connection_to_db();
}

function my_cms_error_404() {
    http_response_code(404);
    global $my_cms_skin;
    if (file_exists(__DIR__ . "/skins/{$my_cms_skin}/404.php")) {
        include __DIR__ . "/skins/{$my_cms_skin}/404.php";
    } else {
        include __DIR__ . "/404.php";
    }
}

function create_blog_page() {
    if ( isset($_POST['title'])  && isset($_POST['slug']) && isset($_POST['text']) ) {
            $title = $_POST['title'];
            $slug  = $_POST['slug'];
            $text  = $_POST['text'];

            connect_to_db();
            global $conn;

            $stmt = $conn->prepare("INSERT INTO blog (title, slug, text) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $slug, $text);
            $stmt->execute();
            $stmt->close();

            close_connection_to_db();

            header('Location: '. $wg_path . "blog/" . $slug);
        }
}

function my_cms_homepage() {
    ?>
    <h1>Домашняя страница</h1>
    <p>Это домашняя страница MY_CMS</p>
    <?php
}
function my_cms_blog_page() {
    connect_to_db();
    global $conn, $wg_path;
    
    // Подготовка запроса для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT title, slug, text FROM blog LIMIT 3;");
    $stmt->execute();
    $result = $stmt->get_result();

    // Process the result set
    if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        ?>
        <div class="blog-wrapper">
            <h1><a href="<?="{$wg_path}blog/{$row['slug']}";?>"><?=htmlspecialchars($row['title']);?></a></h1>
            <a href="<?="{$wg_path}blog/{$row['slug']}";?>"><?=htmlspecialchars($row['text']);?></a>
        </div>
        <?php
    }
    } else if ($result->num_rows === 0) {

    }
    
    // Закрытие подготовленного запроса
    $stmt->close();
    
    close_connection_to_db();
}
function get_locale() {
    return Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
function get_html_title() {
    return 'my_cms';
}
function get_styles() {
    global $my_cms_skin, $wg_path, $my_cms_extensions;
    $output = "";
    #$output .= "<link rel=\"stylesheet\" href=\"{$wg_path}css/cms.css\">";
    $output .= "<link rel=\"stylesheet\" href=\"{$wg_path}skins/{$my_cms_skin}/css/skin.css\">";
    foreach ($my_cms_extensions as $ext) {
        if (file_exists(__DIR__ . "/extensions/{$ext}/css/ext.css")) {
            $output .= "<link rel=\"stylesheet\" href=\"{$wg_path}extensions/{$ext}/css/ext.css\">";
        }
    }
    $output .= "<link rel=\"stylesheet\" href=\"{$wg_path}css/cms.css\">";
    return $output;
}
function get_heder_scripts() {
    global $my_cms_skin, $wg_path, $my_cms_extensions;
    $output = "";
    $output .= "<script src=\"{$wg_path}js/cms.js\" defer></script>";
    $output .= "<script src=\"{$wg_path}skins/{$my_cms_skin}/js/skin.js\" defer></script>";
    foreach ($my_cms_extensions as $ext) {
        if (file_exists(__DIR__ . "/extensions/{$ext}/js/ext.js")) {
            $output .= "<script src=\"{$wg_path}extensions/{$ext}/js/ext.js\" defer></script>";
        }
    }
    return $output;
}
function load_skin() {
    global $my_cms_skin;
    include __DIR__ . "/skins/{$my_cms_skin}/skin.php";
}
function load_extension($ext) {
    include __DIR__ . "/extensions/{$ext}/functions.php";
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