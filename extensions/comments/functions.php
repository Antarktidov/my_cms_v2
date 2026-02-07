<?php
include __DIR__ . "/../../go_away.php";

my_cms_add_hook('single_blog_after_content', function($blog) {
    blog_comments($blog['id']);
});

function blog_comments($blog_id) {
    connect_to_db();
    global $conn;
    
    // Подготовка запроса для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT text FROM comment WHERE blog_id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<h2>Комментарии:</h2>";
    include __DIR__ . "/new-comment.php";
    ?><div class="comments"><?php
    // Process the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="comment">
                <?=$row['text'];?>
            </div>
            <?php
        }
    } else {
        echo "<p>Комментариев пока нет</p>";
    }

    ?></div><?php
    
    // Закрытие подготовленного запроса
    $stmt->close();
}

function post_blog_comment() {
    if ( isset($_POST["comment"]) && isset($_POST["blog_id"]) ) {
        $text = $_POST["comment"];
        $blog_id = $_POST["blog_id"];
        connect_to_db();
        global $conn, $wg_path;

        $stmt = $conn->prepare("INSERT INTO comment (text, blog_id) VALUES (?, ?)");
        $stmt->bind_param("ss", $text, $blog_id);
        $stmt->execute();
        $stmt->close();

        // Source - https://stackoverflow.com/a/6768831
        // Posted by ax., modified by community. See post 'Timeline' for change history
        // Retrieved 2026-02-07, License - CC BY-SA 4.0
        $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        header('Location: '. $actual_link);
    }
}