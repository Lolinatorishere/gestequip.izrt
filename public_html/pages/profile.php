<?php
include_once __DIR__ . "/../frontend/html/html_head.php";
include_once __DIR__ . "/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Profile";
$css_path = array(
    "../frontend/css/common.css",
    "../frontend/css/pages/profile.css",
    "../frontend/css/sidebar/sidebar.css",
    );

$js_path = array("../frontend/js/common/backend_requests.js"
                ,"../frontend/js/pages/profile/generateHtml.js"
                ,"../frontend/js/pages/profile/user_info.js"
                ,"../frontend/js/pages/profile/printProfile.js"
                );

$navbar = "default";

make_head($language, $page_name, $css_path, $js_path);

?>
    <body>
        <div class="body-container">
            <div class="under-navbar-content">
                <?php include_once __DIR__ . "/../frontend/html/sidebar/sidebar.php" ?>
                <div class="main-content-corner">
                    <div class="main-content">
                        <div class="profile-content">
                        <div class="content-first">
                            <div class="profile-align-vertical">
                                <div class="profile-align-horizontal">
                                    <div class="top-profile" id="profile-info">
                                        <div class="user-info-title">
                                            User Information:
                                        </div>
                                        <div class = "user-info">
                                            <div class="users-name">
                                                <div class="users-name-title">
                                                    Name:
                                                </div>
                                                <div id="users_name">
                                                </div>
                                            </div>
                                            <div class="user-username">
                                                <div class="username-title">
                                                    Username:
                                                </div>
                                                <div id="username">
                                                </div>
                                            </div>
                                            <div class="user-email">
                                                <div class="email-title">
                                                    Email:
                                                </div>
                                                <div id="email">
                                                </div>
                                            </div>
                                            <div class="phone_number">
                                                <div class="phone_number-title">
                                                    Phone Number:
                                                </div>
                                                <div class="number-concat">
                                                    <div id="phone_number">
                                                    </div>
                                                    <div id="regional_indicator">
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                printUser()
                                            </script>
                                        <?php 
                                        if($_SESSION["user_type"] === "Manager" || $_SESSION["user_type"] === "Admin"){
                                            echo('
                                                </div>
                                                <div class="user-controlls" id="user-controlls">
                                                <script>
                                                    loadUserControls();
                                                </script>
                                            </div>
                                            ');
                                        }
                                        ?>
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
                                            <div id="your-groups" class="group-your">
                                                <div class="your-groups">
                                                    <div class="group-controlls" id="group-controlls">
                                                        page ctrl
                                                    </div>
                                                    <div class="groups-content" id="groups-content">
                                                        <div class="group-content-title" id="group-content-title">
                                                            <div class="group-title-name">
                                                                Name
                                                            </div>
                                                            <div class="group-title-status">
                                                                Status
                                                            </div>
                                                        </div>
                                                        <div class="group-content-items" id="group-content-items">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                 printUserGroups();
                                            </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content-second">
                            <div class="equipment-align-vertical">
                                <div class="equipment-align-horizontal">
                                    <div class="equipment-profile">
                                            <div id="your-equipment" class="equipment-your">
                                                <div class="your-groups">
                                                    <div class="group-controlls" id="group-controlls">
                                                        page ctrl
                                                    </div>
                                                    <div class="groups-content" id="groups-content">
                                                        <div class="group-content-title" id="group-content-title">
                                                            <div class="group-assigned-title-name">
                                                                group
                                                            </div>
                                                            <div class="equipment-type-title">
                                                                type
                                                            </div>
                                                            <div class="equipment-brand-title">
                                                                brand
                                                            </div>
                                                            <div class="equipment-purchase-date-title">
                                                                purchase date
                                                            </div>
                                                            <div class="status">
                                                                status
                                                            </div>
                                                        </div>
                                                        <div class="equipment-content-items" id="equipment-content-items">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <script>
                                             printUserEquipments();
                                        </script>
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
