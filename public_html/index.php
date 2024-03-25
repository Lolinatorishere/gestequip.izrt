<?php
    require_once __dir__."/frontend/html/html_head.php";
    require_once __dir__."/frontend/html/form/html_formgen.php";
    require_once __dir__."/frontend/html/div_gen.php";

    $language = "en";
    $page_name = "Gestor Equipamentos";
    $css_path = "/frontend/css/index.css";
    $js_path = ""; // not necessary to input because this page doesnt require js files

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
?>

<?=make_head($language , $page_name , $css_path , $js_path)?>
    <body>
        <div class="form-positioner">
            <?=make_form($form_config , $form_action , $form_method , $form_inputs)?>
        </div>
    </body>
</html>