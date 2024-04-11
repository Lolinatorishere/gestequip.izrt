<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";

$language = "en";
$page_name = "Equipment";
$css_path = array("../frontend/css/common.css",
                  "../frontend/css/pages/equipment.css",
                  "../frontend/css/sidebar/sidebar.css",
                  "../frontend/css/navbar/navbar.css");

$js_path = "";

    $form_action = "/pages/dashboard.php";
    $form_method = "post";
    $form_config = array(
        "form_container" => "",
        "form_class" => "sign-in-form",
        "form_title_class" => "form-title",
        "form_title" => "Please sign in",
        "form_div_class" => "form-input-container",
        "button_div_class" => "button-input-container",
    );

    $form_input_array = array(
        "label_for" => array("inputEmail" , "inputPassword"),
        "label_class" => array("" , ""),
        "label_text" => array("" ,""),
        "label_for" => array("inputEmail" , "inputPassword"),
        "input_type" => array("email" , "password"),
        "input_id" => array("inputEmail" , "inputPassword"),
        "input_class" => array("form-input" , "form-input"),
        "input_placeholder" => array("Email address" , "Password"),
        "input_required" => array("true" , "true"),
        "input_maxlength" => array("50 " , ""),
        "input_autofocus" => array("true " , "") 
    );

    $form_button_array = array(
        "button_href" => array(""),
        "button_class" => array("login-button"),
        "button_type" => array("submit"),
        "button_name" => array("submit"),
        "button_value" => array("submit"),
        "button_text" => array("Sign in")
    );

    $form_inputs =  array(
        "form_input" => make_form_inputs($form_input_array),
        "button" => make_form_buttons($form_button_array),
        "input_seperator" => "",
        "button_seperator" => ""
    );
$navbar = "default";

make_head($language , $page_name , $css_path , $js_path);
?>

    <body>
        <div class="body-container">
            <?=make_navbar($navbar)?>
            <?="<script src=\"../frontend/js/navbar/profile_dropdown.js\"></script>"?>
            <div class="under-navbar-content">
                <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
                <div class="main-content-corner">
                    <div class="main-content">
                        <div class="main-content-top">
                            stuff
                        </div>
                        <div class="main-content-bottom">
                            stuff
                        </div>
                </div>
            </div>
        </div>
    </body>
</html>