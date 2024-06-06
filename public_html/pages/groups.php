<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";
include_once __DIR__."/../frontend/html/div_gen.php";
include_once __DIR__."/../frontend/html/form/html_formgen.php";

$language = "en";
$page_name = "Groups";
$css_path = array("../frontend/css/common.css",
                  "../frontend/css/sidebar/sidebar.css");
$js_path = "";

$navbar = "default";
make_head($language , $page_name , $css_path , $js_path);
?>

    <body>
        <div class="body-container">
            <div class="under-navbar-content">
                <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
                <div class="main-content-corner">
                    <div class="main-content">

                </div>
            </div>
        </div>
    </body>
</html>
