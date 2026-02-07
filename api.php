<?php
define("MY_CMS", "web-api");
include __DIR__ . "/localSettings.php";
include __DIR__ . "/functions.php";
header('Content-Type: application/json; charset=utf-8');
foreach ($my_cms_extensions as $ext) {
    load_extension_api($ext);
}
http_response_code(404);
$output = [
    "code" => 404,
];
echo json_encode($output);