<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Dashboard";
$css_path = array("../frontend/css/common.css",
                  "../frontend/css/pages/dashboard.css",
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
                    <div class="main-content-top">
                        <div class="main-content-positioner-top-left">
                            <div class="top-left-userinfo">
                                <div 
                                class="top-left-item-userinfo"
                                id = "first-top-left-item-userinfo">
                                    <div class="top-left-item-icon-userinfo">
                                        <span class="material-symbols-outlined">
                                            account_circle
                                        </span>
                                    </div>
                                    <div class="top-left-item-text-userinfo">
                                        <p class="top-right-item-text-title">
                                            Users
                                        </p>
                                        <p class="top-left-item-text-number">
                                            0
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-content-positioner-top-right">
                            <div class="top-right-notifications"
                            id="notification-bar">
                                <div class="top-right-notifications-title">
                                    <span class="material-symbols-outlined">
                                        notifications
                                    </span>
                                    <p class="top-right-notifications-title-text">
                                        Notifications
                                </div>
                            </div>
                            <div class="top-right-totals">
                                <div class="top-right-totals-title">
                                        Totals 
                                </div>
                                <div 
                                class="top-right-item-totals"
                                id = "first-top-right-item-totals">
                                    <div class="top-right-item-icon-totals">
                                        <span class="material-symbols-outlined">
                                            account_circle
                                        </span>
                                    </div>
                                    <div class="top-right-item-text-totals">
                                        <p class="text-totals-name">
                                            Users
                                        </p>
                                        <p class="text-totals-number">
                                            0
                                        </p>
                                    </div>
                                </div> 
                                <div class="top-right-item-totals">
                                    <div class="top-right-item-icon">
                                        <span class="material-symbols-outlined">
                                            computer
                                        </span>
                                    </div>
                                    <div class="top-right-item-text-totals">
                                        <p class="top-right-item-text-title">
                                            Equipment
                                        </p>
                                        <p class="top-right-item-text-number">
                                            0
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-content-bottom">
                        <div class="main-content-bottom-positioner">
                            <div class="bottom-temporary">
                                content chop
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
