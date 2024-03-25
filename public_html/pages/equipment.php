<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Dashboard";
$css_path = array("../frontend/css/common.css",
                  "../frontend/css/sidebar/sidebar.css",
                  "../frontend/css/navbar/navbar.css");

$js_path = "";

$navbar = "default";

make_head($language , $page_name , $css_path , $js_path);
?>
<body>
<div class="body-container">
        <?=make_navbar($navbar)?>
        <?="<script src=\"../frontend/js/navbar/profile_dropdown.js\"></script>"?>
        <div class="under-navbar-content">
            <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
        </div>
    </body>
</html>