<?php
include_once __DIR__ . "/../frontend/html/html_head.php";
include_once __DIR__ . "/../frontend/html/navbar/html_nav.php";
include_once __DIR__ . "/../backend/query/user_query.php";

$language = "en";
$page_name = "Profile";
$css_path = array(
    "../frontend/css/common.css",
    "../frontend/css/pages/profile.css",
    "../frontend/css/sidebar/sidebar.css",
    "../frontend/css/navbar/navbar.css",
);

$js_path = "";

$navbar = "default";

make_head($language, $page_name, $css_path, $js_path);

?>
    <body>
        <div class="body-container">
            <?= make_navbar($navbar) ?>
            <?= "<script src=\"../frontend/js/navbar/profile_dropdown.js\"></script>" ?>
            <div class="under-navbar-content">
                <?php include_once __DIR__ . "/../frontend/html/sidebar/sidebar.php" ?>
                <div class="main-content-corner">
                    <div class="main-content">
                        <div class="profile-content">
    
                        <div class="content-first">
    
                            <div class="profile-align-vertical">
                                <div class="profile-align-horizontal">
                                    <div class="top-profile">
                                        <?php $user_info = request_info();?>
                                        <div class="profile-vertical-align">
                                            <div class="profile-horizontal-align">
                                                <div class="profile-tab">
                                                  <iframe src="/frontend/iframes/profile/profile_info.php" frameborder="0"></iframe> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                //todo fix the css for the items and then remove this reminder
                                //why is it in php because i dont want it to be shown in the website
                            ?>
                            <div class="groups-align-vertical">
                                <div class="groups-align-horizontal">
                                    <div class="groups-profile">
                                        <div class="groups-iframe">
                                            <iframe src="/frontend/iframes/profile/group_info.php" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content-second">
                            <div class="equipment-align-vertical">
                                <div class="equipment-align-horizontal">
                                    <div class="equipment-profile">
                                        <iframe src="/frontend/iframes/profile/equipment_info.php" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </body>
</html>