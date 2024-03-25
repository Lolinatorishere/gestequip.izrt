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