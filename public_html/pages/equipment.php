<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Equipment";
$css_path = array("../frontend/css/common.css"
                 ,"../frontend/css/pages/equipment.css"
                 ,"../frontend/css/sidebar/sidebar.css"
                 ,"../frontend/css/navbar/navbar.css");

$js_path = array("../frontend/js/iframes/equipment/tabbar.js");

$navbar = "default";

make_head($language , $page_name , $css_path , $js_path);
?>

    <body>
        <div class="body-container">
            <div class="under-navbar-content">
                <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
                <div class="main-content-corner">
                    <div class="main-content">
                        <div class="equipment-content">
                            <iframe src="" id = "equipment_internal" frameborder="0">
                            </iframe>
                        </div>
                        <script> 
                            createEquipmentContent();
                        </script> 
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>