<?php
include __DIR__ . "/../../go_away.php";

function submit_contact_form() {
    verifying_csrf_token();
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        $ip = get_ip();
        connect_to_db();
        global $conn, $wg_path;

        $stmt = $conn->prepare("INSERT INTO contact_from (ip, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $ip, $message);
        $stmt->execute();
        $stmt->close();

        echo "<p>Сообщение сохранено. Спасибо!</p>";
    }
}