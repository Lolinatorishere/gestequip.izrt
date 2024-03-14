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
            </div>
            <div>");
    // Looping through each navigation link item
    if($nav_conf ["links"] === true){
        echo("
                <ul class=\"$nav_links[ul_class]\">
        ");
        foreach($nav_links["li_items"] as $item){
            echo(   //gets the li_class and a_class from the array and sets
                    //those items to the class attribute of the li and a tags
                    "
                    <li class=\"$item[li_class]\">
                        <a class=\"$item[a_class]
                        ". 
                        // This function checks if the current link is the active link
                        active_link($item, $nav_conf) 
                        .
                        // prints the link and the text
                        " 
                        \" href=\"$item[a_href]\">
                            $item[a_text]  
                        </a>
                    </li>
            ");
        }
    }   
    echo("
                    </ul>  <!-- End of the list for the navigation links -->
            </div>
            <div
        </div>
    </nav>  <!-- End of the navigation bar -->
    ");
}   

function active_link($item, $nav_conf){
    if($item["a_text"] === $nav_conf["current_link"]){
        return("active");
    }
}

// make_li_items takes an array of links and returns an array of list items
//
//    links is an example of what a link to be printed in the navigation bar looks like:
//    text, href, li_class and a_class are arrays that contain strings that are used
//    to generate and configure the navigation bar.
//
//    
//    $links = array(
//        "text" => array(""), //the number of elements in this array determines the number of links
//        "href" => array(""),
//        "li_class" => array(""),
//        "a_class" => array(""),
//    );
//

function make_li_items($links){

    $li_items = array();
    if(isset($links["text"])){
        for($i = 0; $i < count($links["text"]); $i++){
            if($links["a_class"][$i] === ""){
                $links["a_class"][$i] = "nav-link";
            }
            if($links["li_class"][$i] === ""){
                $links["li_href"][$i] = "nav-item";
            }
            $li_items[$i] = array(
                "li_class" => $links["li_class"][$i],
                "a_class" => $links["a_class"][$i],
                "a_href" => $links["href"][$i],
                "a_text" => $links["text"][$i]
            );
        }
    }
    return $li_items;
}
?>