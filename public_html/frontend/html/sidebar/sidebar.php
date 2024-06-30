<?php
function create_profile_div(){
    $user_type = "";
    if(isset($_SESSION["user_type"])){
        $user_type = "
                        <div id=\"profile-user-type\" class=\"profile-user-type\">
                            " . $_SESSION["user_type"] . "
                        </div>
                     ";
    }

    $profile = "
        <div class=\"profile-div\">
            ". $user_type . "
            <div class=\"profile-username\">" . $_SESSION["users_name"] . "</div>
            <div id=\"profile-logout-button\" class=\"profile-logout\">
                Sign out
            </div>
        </div>
        ";
    return $profile;
}


    $sidebar_link = array(
         "../../../pages/profile.php",
         "../../../pages/groups.php",
    );
    if($_SESSION["user_type"] === "Admin"){
        array_push($sidebar_link , "../../../pages/users.php");
    }
    array_push($sidebar_link , "../../../pages/equipment.php" , "../../../pages/reports.php");

    $sidebar_icon = array(
         "account_circle",
         "event_list"
    );
    if($_SESSION["user_type"] === "Admin"){
        array_push($sidebar_icon , "group");
    }
    array_push($sidebar_icon , "computer" , "summarize");

    $sidebar_content = array(
        "Profile",
        "Groups",
    );
    if($_SESSION["user_type"] === "Admin"){
        array_push($sidebar_content  , "Users");
    }
    array_push($sidebar_content , "Equipment");

    $page_compare = array(
        "profile",
        "groups",
    );
    if($_SESSION["user_type"] === "Admin"){
        array_push($page_compare , "users");
    }
    array_push($page_compare , "equipment");
// This is the sidebar that will be used in the dashboard page
// The sidebar is a div that contains 7 sidebar-items
// Each sidebar-item is a link to a different page
    $current_page = basename($_SERVER['REQUEST_URI']);
    echo("<div class=\"sidebar\"><div class=\"sidebar-nav\">");
    for($i = 0 ; $i < count($page_compare); $i++){
        echo("
            <div class=\"sidebar-item\" ");
            if($i == 0){
                echo("id=\"first-sidebar-item\"");
            }
        echo(">
                <a href=\"". $sidebar_link[$i] ."\">
                    <div class=\"sidebar-content\"");
                        if($current_page == $page_compare[$i] . '.php'){
                            echo("id=\"current-page\"");
                        }
                        echo("><span class=\"material-symbols-outlined sidebar-content-icon\">"
                            . $sidebar_icon[$i] . 
                        "</span>
                        <p class=\"sidebar-content-text\">" . $sidebar_content[$i] . "</p>
                    </div>
                </a>
            </div>
        ");
    }
    echo("</div>");
    echo("<div class=\"sidebar-buffer\"></div>");
    echo("<div class=\"sidebar-profile\">
          <div class=\"profile-controls\">"
    );
        echo(create_profile_div());
    echo("</div>
          <script src=\"/frontend/js/sidebar/sidebar.js\"></script>
          </div>
          </div>");  
?>
