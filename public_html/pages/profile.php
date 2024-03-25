<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";
include_once __DIR__."/../frontend/html/div_gen.php";
include_once __DIR__."/../frontend/html/form/html_formgen.php";

$language = "en";
$page_name = "Dashboard";
$css_path = array("../frontend/css/pages/dashboard.css",
                  "../frontend/css/sidebar/sidebar.css",
                  "../frontend/css/navbar/navbar.css");
$js_path = "";
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

$search = make_form(
    $form_config,
    $form_action,
    $form_method,
    $form_inputs
);


$navbar = array($search , $profile);
make_head($language , $page_name , $css_path , $js_path);
?>
<body>
<div class="body-container">
        <?=make_navbar($navbar)?>
        <?="<script src=\"../frontend/js/navbar/profile_dropdown.js\"></script>"?>
        <div class="under-navbar-content">
            <?php include_once __DIR__."/../frontend/html/sidebar/sidebar.php"?>
        </div>
    </body>
</html>