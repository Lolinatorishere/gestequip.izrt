<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Equipment";
$css_path = array("../frontend/css/common.css"
                 ,"../frontend/css/pages/equipment/equipment.css"
                 ,"../frontend/css/pages/equipment/tabs_common.css"
                 ,"../frontend/css/pages/equipment/tabs/grp_eq.css"
                 ,"../frontend/css/pages/equipment/tabs/sch_eq.css"
                 ,"../frontend/css/sidebar/sidebar.css"
                 );

$js_path = array("../frontend/js/pages/equipment/tabbar_functionality.js"
                ,"../frontend/js/common/backend_requests.js"
                ,"../frontend/js/pages/common/equipment_requests.js"
                ,"../frontend/js/pages/common/user_requests.js"
                ,"../frontend/js/pages/equipment/tabs/group_tab.js"
                ,"../frontend/js/pages/equipment/tabs/search_tab.js"
                );

if($_SESSION["user_type"] === "Manager" || $_SESSION["user_type"] === "Admin"){
    array_push($css_path , "../frontend/css/pages/equipment/tabs/add_eq.css");

    array_push($js_path , "../frontend/js/pages/equipment/tabs/create_tab.js");
}

if($_SESSION["user_type"] === "Admin"){
    array_push($css_path , "../frontend/css/pages/equipment/tabs/modtyp.css");
    array_push($css_path , "../frontend/css/pages/equipment/tabs/all_eq.css");
    array_push($css_path , "../frontend/css/pages/equipment/tabs/del_eq.css");
    array_push($css_path , "../frontend/css/pages/equipment/tabs/log_eq.css");

    array_push($js_path , "../frontend/js/pages/equipment/tabs/modify_tab.js");
    array_push($js_path , "../frontend/js/pages/equipment/tabs/all_tab.js");
    array_push($js_path , "../frontend/js/pages/equipment/tabs/delete_tab.js");
    array_push($js_path , "../frontend/js/pages/equipment/tabs/logs.js");
}

$navbar = "default";

make_head($language , $page_name , $css_path , $js_path);
?>

    <body>
        <div class="body-container">
            <div class="under-navbar-content">
                <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
                <div class="main-content-corner">
                    <div class="main-content">
                        <div class="equipment-content" id="equipment_internal">
                            <?php include_once __DIR__."/../frontend/html/pages/equipment/tabbar.php"?>
                            <div id="tab-content" class="tab-content">
                            <div>
                        </div>
                        <script> 
                            onloadTabsFunctions();
                        </script> 
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
