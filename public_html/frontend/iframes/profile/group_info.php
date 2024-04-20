<?php

include_once __DIR__."/../../../frontend/html/html_head.php";
include_once __DIR__."/../../../backend/query/user_query.php";
    
function div_headgen($class){
    $ret = "<div class=\"" . $class . "\">";
    return $ret;
}

$language = "en";
$page_name = "Profile";
$css_path = array(
    "../../css/iframes/profile/groups.css"
);

$js_path = "";

$groups = get_the_users_groups();
make_head($language, $page_name, $css_path, $js_path);
?>

    <body>
        <div class = "group-main">
            <div class="group-total">
                <div class="group-total-align-vertical">
                    <div class="group-total-align-horizontal">
                        <div class="group-total-content">
                            Groups <?=count($groups)?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="groups-columns">
                <div class="group-item-height">
                    <div class="group-item-align-vertical">
                        <div class="group-item-align-horizontal" id="item-row-title">
                            <div id="item-number-column-title">
                                <div id="center-verticaly">
                                    <div id="center-horizontaly-title">
                                        Number
                                    </div>
                                </div>
                            </div>
                            <div id="item-name-column-title"> 
                                <div id="center-verticaly">
                                    <div id="center-horizontaly-title">
                                        Name
                                    </div>
                                </div>
                            </div>
                            <div id="item-type-column-title"> 
                                <div id="center-verticaly">
                                    <div id="center-horizontaly-title">
                                        Type
                                    </div>
                                </div>
                            </div>
                            <div id="item-status-column-title"> 
                                <div id="center-verticaly">
                                    <div id="center-horizontaly-title">
                                        Status
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $j = 1;
                foreach($groups as $group){
                    $value = "
                    <div class=\"group-item-height\">
                        <div class=\"group-item-align-vertical\">
                            <div class=\"group-item-align-horizontal\"";
                                if($j%2==0){
                                        $value .= "id=\"item-row\"";
                                }
                                $value .= ">
                                <div id=\"item-number-column\">
                                    <div id=\"center-verticaly\">
                                        <div id=\"center-horizontaly-first\">" 
                                            . 
                                            $j 
                                            . 
                                        "</div>
                                    </div>
                                </div>
                                <div id=\"item-name-column\"> 
                                    <div id=\"center-verticaly\">
                                        <div id=\"center-horizontaly\">
                                            <p>"
                                            .
                                            $group["group_name"]
                                            .
                                            "</p>
                                        </div>
                                    </div>
                                </div>
                                <div id=\"item-type-column\"> 
                                    <div id=\"center-verticaly\">
                                        <div id=\"center-horizontaly\">"
                                            .
                                            $group["group_type"]
                                            .
                                        "</div>
                                    </div>
                                </div>
                                <div id=\"item-status-column\"> 
                                    <div id=\"center-verticaly\">
                                        <div id=\"center-horizontaly\">";
                                            if($group["group_status"] != 0){
                                                $value .= "Active";
                                            }else{
                                                $value .= "Inactive";
                                            }
                                        $value .=
                                        "</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                    $j++;
                    echo($value);
                }
            ?>
            </div>
        </div>
    </body>
</html>
test