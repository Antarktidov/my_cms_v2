<?php
include __DIR__ . "/../../go_away.php";

$output = [];

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (isset($_GET['blog_id'])) {
        $blog_id = $_GET['blog_id'];

        connect_to_db();
        global $conn;
    
        // Подготовка запроса для защиты от SQL-инъекций
        $stmt = $conn->prepare("SELECT id, title, slug, text FROM blog WHERE id = ?");
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        // Process the result set
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $data[] = [
                    "id" => htmlspecialchars($row['id']),
                    "title" => htmlspecialchars($row['title']),
                    "slug" => htmlspecialchars($row['slug']),
                    "text" => htmlspecialchars($row['text']),
                ];
            }
            $output = [
                "code" => 200,
                "blog_info" => $data[0],
            ];
        } else {
            http_response_code(404);
            $output = [
                "code" => 404,
            ];
        }
        echo json_encode($output);
        $stmt->close();
        exit;
    }
}