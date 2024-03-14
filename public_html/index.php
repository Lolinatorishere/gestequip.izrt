<?php
    $head_path = __dir__."/frontend/html/html_head.php";
    $navbar_path = __dir__."/frontend/html/navbar/html_nav.php";
    $nav_conf_path = __dir__."/frontend/html/navbar/nav_conf.php";
    $login_path = __dir__."/frontend/html/form/html_formgen.php";
    require_once $head_path;
    require_once $navbar_path;
    require_once $nav_conf_path;    
    require_once $login_path;       
    $language = "en";
    $page_name = "Gestor Equipamentos";
    $css_path = "/frontend/css/index.css";
    $nav_conf["links"] = false;
    $nav_conf["current_link"] = "Home";
    $links = array(
        "text" => array(""),
        "hrefs" => array(""),
    );
    $form_config = array(
        "form_class" => "form-signin",
        "form_title_class" => "h3 mb-3 font-weight-normal",
        "form_title" => "Please sign in"
    );
    $nav_links = "";
    $form_input_array = array(
        "label_for" => array("inputEmail" , "inputPassword"),
        "label_class" => array("visually-hidden" , "visually-hidden"),
        "label_text" => array("Email address" ,"Password"),
        "label_for" => array("inputEmail" , "inputPassword"),
        "input_type" => array("email" , "password"),
        "input_id" => array("inputEmail" , "inputPassword"),
        "input_class" => array("form-control" , "form-control"),
        "input_placeholder" => array("Email address" , "Password"),
        "input_required" => array("true" , "true"),
        "input_maxlength" => array("50 "),
        "input_autofocus" => array("true ") 
    );
    $form_button_array = array(
        "button_href" => array(""),
        "button_class" => array("w-100 btn btn-lg btn-primary"),
        "button_type" => array("submit"),
        "button_name" => array("submit"),
        "button_value" => array("submit"),
        "button_text" => array("Sign in")
    );
    $form_action = "backend/login/login.php";
    $form_method = "post";
    $form_inputs =  array(
        "form_input" => make_form_inputs($form_input_array),
        "button" => make_form_buttons($form_button_array)
    );

echo("<html>");
    make_head($language , $page_name , $css_path);
    echo("<body >");
        make_cdn_scripts();
        make_navbar($nav_conf , $nav_links);
        make_form($form_config , $form_action , $form_method , $form_inputs);
    echo("
    </body>
</html>
");
?>