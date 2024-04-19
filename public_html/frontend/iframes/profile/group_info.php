<?php

include_once __DIR__."/../../../frontend/html/html_head.php";
include_once __DIR__."/../../../backend/query/user_query.php";
    
$language = "en";
$page_name = "Profile";
$css_path = array(
    "../../css/common.css",
    "../../css/iframes/profile_groups.css"
);

$js_path = "";

make_head($language, $page_name, $css_path, $js_path);
?>

    <body>
        <div class = "group-main">
            test
        </div>
        <?php
            // $groups = get_the_users_groups();
            // if(count($groups) == 0){
            // }else{
            // }
        ?>
    </body>
</html>
