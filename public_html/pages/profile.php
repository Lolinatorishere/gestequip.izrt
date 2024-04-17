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
                            <div class="top-profile">
                                <?php
                                $user_info = request_info();
                                ?>
                                <div class="profile-vertical-align">
                                <div class="profile-horizontal-align">
                                <div class="profile-tab">

                                    <div class="profile-users-names">
                                        <div class="profile-name">
                                            <div class="internal-name-horizontal">
                                            <div class="internal-name-vertical">
                                                <?= $user_info["name"]; ?>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="profile-user-username">
                                            <div class="internal-username-horizontal">
                                            <div class="internal-username-vertical">
                                                <?= $user_info["username"] ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile-email-vertical">
                                        <div class="profile-email">
                                            <div class="internal-title-email">
                                                <p>Email:</p>
                                            </div>
                                            <div class="internal-email-horizontal">
                                            <div class="internal-email-vertical">
                                                <?= $user_info["email"] ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile-phone-vertical">
                                        <div class="profile-phone">
                                            <div class="internal-title-phone">
                                                <p>Phone:</p>
                                            </div>
                                            <div class="internal-phone-horizontal">
                                            <div class="internal-phone-vertical">
                                                <?= $user_info["phone_number"] ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="profile-acc-status-vertical">
                                        <div class="profile-acc-status">
                                            <div class="internal-title-acc-status">
                                                <p>Status:</p>
                                            </div>
                                            <div class="internal-acc-status-horizontal">
                                            <div class="internal-acc-status-vertical">
                                                <?= $user_info["acc_status"] ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile-reg-date">
                                        <div class="internal-title-registration">
                                            <p>Registration date:</p>
                                        </div>
                                        <div class="internal-reg-date">
                                            <?= $user_info["reg_date"] ?>
                                        </div>
                                    </div>

                                </div>
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