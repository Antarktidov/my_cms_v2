
<?php
define("MY_CMS", "web");
include __DIR__ . "/localSettings.php";
include __DIR__ . "/functions.php";
foreach ($my_cms_extensions as $ext) {
    load_extension($ext);
}
load_skin();