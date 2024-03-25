<?php
include_once __DIR__."/../php_frontend_functions.php";
include_once __DIR__."/../form/html_formgen.php";

function defaults(){
$profile = ("
    <div class=\"profile-div\">
        <p class=\"profile-username\">Username </p>
        <div class=\"profile-dropdown\">
            <p href=\"\" class=\"profile-dropdown-item\" id=\"profile-user-type\">
                UserType
            </p>
            <a href=\"\" class=\"profile-dropdown-item\">Settings</a>
            <a href=\"\" class=\"profile-dropdown-item\">Help</a>
            <a href=\"\" class=\"profile-dropdown-item\" id=\"profile-logout-button\">
                Sign out
            </a>
        </a>
    </div>
    ");

$form_config = array(
    "form_class" => "search-form",
    "form_div_class" => "form-input-container",
    "button_div_class" => "button-input-container",
);

$form_input_array = array(
    "label_for" => array("input-search"),
    "input_type" => array("text"),
    "input_id" => array("input-search"),
    "input_class" => array("search-bar"),
    "input_required" => array("false"),
    "input_maxlength" => array("50"),
);

$form_button_array = array(
    "button_href" => array(""),
    "button_class" => array("search-button"),
    "button_type" => array("submit"),
    "button_name" => array("submit"),
    "button_value" => array("submit"),
    "button_text" => array("search")
);

$form_inputs =  array(
    "form_input" => make_form_inputs($form_input_array),
    "button" => make_form_buttons($form_button_array),
);

$form_action = "";
$form_method = "";

$search = make_form(
    $form_config,
    $form_action,
    $form_method,
    $form_inputs
);
return array($search, $profile);
}

// This function generates a navigation bar with given configurations and links
function make_navbar($input){
    convert_to_array($input);
    if($input == "default"){
        $search = defaults()[0];
        $profile = defaults()[1];
        $navbar_content = array($search, $profile);
    }else{
        $navbar_content = $input;   
    }
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