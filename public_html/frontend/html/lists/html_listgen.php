<?php
function make_list($list){
    $ret = ("<ul class=\"$list[ul_class]\">");
    foreach($list["li_items"] as $item){
        $ret .= (   
            //gets the li_class and a_class from the array and sets
            //those items to the class attribute of the li and a tags
            "
            <li class=\"$item[li_class]\">
                \" href=\"$item[a_href]\">
                    $item[a_text]  
                </a>
            </li>
        ");
    }
    $ret .= ("</ul>");
    return $ret;
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