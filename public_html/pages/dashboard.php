<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";
include_once __DIR__."/../frontend/html/div_gen.php";
include_once __DIR__."/../frontend/html/form/html_formgen.php";

$language = "en";
$page_name = "Dashboard";
$css_path = "../frontend/css/dashboard.css";

$profile = ("
    <div class=\"profile\">
        <p class=\"profile-name\">Username</p>
        <a href=\"\" class=\"logout-button\">
            Logout
        </a>
    </div>
");

$form_config = array(
    "form_container" => "",
    "form_class" => "search-form",
    "form_title_class" => "",
    "form_title" => "",
    "form_div_class" => "form-input-container",
    "button_div_class" => "button-input-container",
);

$form_input_array = array(
    "label_for" => array(""),
    "label_class" => array("" , ""),
    "label_text" => array("" ,""),
    "label_for" => array("input-search"),
    "input_type" => array("text"),
    "input_id" => array("input-search"),
    "input_class" => array("search-bar"),
    "input_placeholder" => array(""),
    "input_required" => array("false"),
    "input_maxlength" => array("50"),
    "input_autofocus" => array("") 
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
    "input_seperator" => "",
    "button_seperator" => ""
);

$search = make_form(
    $form_config,
    $form_action,
    $form_method,
    $form_inputs
);

$navbar = array($search , $profile);
make_head($language , $page_name , $css_path);
?>
    <body>
    <div class="body-container">
        <?php 
        echo(make_navbar($navbar));
        ?>
        <div class="under-navbar-content">
            <div class="sidebar">
                <a href="" class="sidebar-item" id="first-sidebar-item">Dashboard</a>
                <a href="" class="sidebar-item">Dashboard</a>
                <a href="" class="sidebar-item">Dashboard</a>
            </div>
            <div class="main-content">
                <p>content chop</p>
        </div>
    </div>
    </body>
</html>