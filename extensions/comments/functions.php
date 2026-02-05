<?php
include __DIR__ . "/../../go_away.php";

my_cms_add_hook('single_blog_after_content', function($blog) {
    blog_comments($blog['id']);
});

function blog_comments($blog_id) {
    connect_to_db();
    global $conn;
    $sql = "SELECT text FROM comment WHERE blog_id='$blog_id'";
    $result = $conn->query($sql);

    // Process the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        ?><div class="comments"><?php
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="comment">
                <?=$row['text'];?>
            </div>
            <?php
        }
        ?></div><?php
}}