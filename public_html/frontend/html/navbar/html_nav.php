<?php
// This function generates a navigation bar with given configurations and links
function make_navbar($navbar_content){
    
    // Starting to $ret .=  out the HTML for the navigation bar
    $ret = ("
    <nav class=\"navbar\">
            <div class=\"navdiv\">");
        for($i = 0 ; $i < count($navbar_content); $i++){
            $ret .=("
                $navbar_content[$i]
            ");
        }
    $ret .= ("
            </div>
    </nav>  <!-- End of the navigation bar -->
    ");
return $ret;
}

function make_links($list_links){
    $current_link = $list_links["current_link"];
    $ret = ("<ul class=\"$list_links[ul_class]\">");
    foreach($list_links["li_items"] as $item){
        $ret .= (   
            //gets the li_class and a_class from the array and sets
            //those items to the class attribute of the li and a tags
            "
            <li class=\"$item[li_class]\">
                <a class=\"$item[a_class]
                "
                . 
                // This function checks if the current link is the active link
                active_link($item, $current_link) 
                .
                // prints the link and the text
                " 
                \" href=\"$item[a_href]\">
                    $item[a_text]  
                </a>
            </li>
        ");
    }
    $ret .= ("</ul>");
    return $ret;
}   

function active_link($item, $current_link){
    if($item["a_text"] === $current_link){
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
            if(!isset($links["li_class"][$i]))  
                $links["a_class"][$i] = "nav-link";
            if(!isset($links["a_class"][$i]))
                $links["li_href"][$i] = "nav-item";
            if(!isset($links["href"][$i]))
                $links["href"][$i] = "#";
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