<?php
include_once __DIR__."/../../../frontend/html/html_head.php";
include_once __DIR__."/../../../backend/query/user_query.php";
    
$language = "en";
$page_name = "Profile";
$css_path = array(
    "../../css/common.css",
    "../../css/iframes/profile/equipment_info.css"
);

$js_path = "";

make_head($language, $page_name, $css_path, $js_path);
$user_equipment = get_user_equipments();
$user_info = request_info();

?>
<body>

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

</body>