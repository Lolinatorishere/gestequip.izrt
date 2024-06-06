<?php
function create_profile_div(){
    $user_type = "";
    if(isset($_SESSION["user_type"])){
        $user_type = "
                        <p id=\"profile-user-type\">
                            " . $_SESSION["user_type"] . "
                        </p>
                     ";
    }

    $profile = "
        <div class=\"profile-div\">
            ". $user_type . "
            <p class=\"profile-username\">" . $_SESSION["users_name"] . "</p>
            <a href=\"\..\backend\session\uncheck.php\" id=\"profile-logout-button\">
                Sign out
            </a>
        </div>
        ";
    return $profile;
}


   $sidebar_link = array(
        "../../../pages/dashboard.php",
        "../../../pages/profile.php",
        "../../../pages/notifications.php",
        "../../../pages/groups.php",
        "../../../pages/users.php",
        "../../../pages/equipment.php",
        "../../../pages/reports.php"
    );

   $sidebar_icon = array(
        "dashboard",
        "account_circle",
        "notifications",
        "event_list",
        "group",
        "computer",
        "summarize"
    );

    $sidebar_content = array(
        "Dashboard",
        "Profile",
        "Notifications",
        "Groups",
        "Users",
        "Equipment",
        "Reports"
    );

    $page_compare = array(
        "dashboard",
        "profile",
        "notifications",
        "groups",
        "users",
        "equipment",
        "reports"
    );
// This is the sidebar that will be used in the dashboard page
// The sidebar is a div that contains 7 sidebar-items
// Each sidebar-item is a link to a different page
    $current_page = basename($_SERVER['REQUEST_URI']);
    echo("<div class=\"sidebar\"><div class=\"sidebar-nav\">");
    for($i = 0 ; $i < 7; $i++){
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
          </div>
          </div>");  
?>
