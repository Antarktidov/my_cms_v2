<?php
include __DIR__ . "/../../go_away.php";
my_cms_header();
global $wg_path;
?>
<aside id="aside">
    <div class="logo"><a style="text-decoration: none; color: unset;" href="<?=$wg_path;?>">My CMS</a></div>
    <nav>
        <a href="https://github.com/Antarktidov/my_cms_v2">GitHub</a>
        <a href="https://github.com/Antarktidov">Developer's Git Hub</a>
        <a href="<?="{$wg_path}blog";?>">Блог</a>
    </nav>
</aside>
<main id="main">
<?php
my_cms_content();
?>
</main>
<?php
my_cms_footer();