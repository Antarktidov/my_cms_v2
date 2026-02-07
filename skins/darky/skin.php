<?php
include __DIR__ . "/../../go_away.php";
my_cms_header();
global $wg_path;
?>
<header id="header">
    <div class="logo"><a style="text-decoration: none; color: unset;" href="<?=$wg_path;?>">My CMS</a></div>
    <nav>
        <a href="https://github.com/Antarktidov/my_cms_v2">GitHub</a>
        <a href="https://github.com/Antarktidov">Developer's Git Hub</a>
        <a href="<?="{$wg_path}blog";?>">Блог</a>
    </nav>
</header>
<?php
my_cms_content();
?>
<footer id="footer">
    <p>Спасибо всем моим учителям, и школьным, и университетским, и виртуальным, и просто учителям жизни, за то, что научили меня всему, в том числе и программировать.</p>
    <p>Автор скина и CMS - Antarktidov</p>
</footer>
<?php
my_cms_footer();