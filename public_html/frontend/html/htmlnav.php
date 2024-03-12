<?php
    // This function generates a navigation bar with given configurations and links
    function make_navbar($nav_conf , $nav_links){
        
        // Extracting configuration for navigation tag, div and title from the passed configuration array
        $navtag_conf = $nav_conf["navtag_conf"];
        $div_conf = $nav_conf["div_conf"];
        $title_conf = $nav_conf["title_conf"];

        // Starting to echo out the HTML for the navigation bar
        echo ("
        <nav class=\"navbar $navtag_conf\">    
            <div class=\"$div_conf\">
                <a class=\"$title_conf[title_class]\" href=\"$title_conf[title_href]\">
                    $title_conf[title_text]  <!-- This is where the title of the navigation bar goes -->
                </a>
                <div 
                class=\"collapse navbar-collapse\"
                id=\"navbarCollapse\">
                        <ul class=\"$nav_links[ul_class]\">");  // Starting the list for the navigation links
        // Looping through each navigation link item
        foreach($nav_links["li_items"] as $item){
            echo("
                            <li class=\"$item[li_class]\">
                                <a class=\"$item[a_class]\" href=\"$item[a_href]\">
                                    $item[a_text]  <!-- This is where the text for each navigation link goes -->
                                </a>
                            </li>
                            ");
        }
                            echo("
                        </ul>  <!-- End of the list for the navigation links -->
                </div>
            </div>
        </nav>  <!-- End of the navigation bar -->
        ");
    }   
?>