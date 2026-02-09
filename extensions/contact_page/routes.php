<?php
include __DIR__ . "/../../go_away.php";

global $path_parts;
if ($path_parts[0] === 'contact') {
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        generate_csrf();
        include __DIR__ . "/contact.php";
    } else if ($_SERVER["REQUEST_METHOD"] === "POST") {
        submit_contact_form();
    }
    exit;
}