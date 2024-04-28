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

?>
<body>
    <div class="profile-equipment-info">
        <?=print_r($user_equipment)?>
    </div>
</body>