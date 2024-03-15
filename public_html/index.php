<?php
    require_once __dir__."/frontend/html/html_head.php";
    require_once __dir__."/frontend/html/navbar/html_nav.php";
    require_once __dir__."/frontend/html/navbar/nav_conf.php";
    require_once __dir__."/frontend/html/form/html_formgen.php";
    require_once __dir__."/frontend/html/div_gen.php";

    $language = "en";
    $page_name = "Gestor Equipamentos";
    $css_path = "/frontend/css/index.css";

    $nav_conf["links"] = true;
    $nav_conf["current_link"] = "Home";

    $links = array(
        "text" => array("Home" , "Features" , "Pricing" , "Disabled"),
        "hrefs" => array("#"),
    );

    $nav_links = array(                                                                                            
        "ul_class" => "navbar-nav ",
        "li_items" => make_li_items($links) // the function make_li_items() is used to generate the navbar links
    );

    $form_action = "backend/login/login.php";
    $form_method = "post";
    $form_config = array(
        "form_container" => "container w-75 mt-5",
        "form_class" => "form-signin",
        "form_title_class" => "h3 mb-3 font-weight-normal text-center",
        "form_title" => "Please sign in",
        "form_div_class" => "",
        "button_div_class" => "div-center w-100"
    );
    $form_input_array = array(
        "label_for" => array("inputEmail" , "inputPassword"),
        "label_class" => array("visually-hidden" , "visually-hidden"),
        "label_text" => array("Email address" ,"Password"),
        "label_for" => array("inputEmail" , "inputPassword"),
        "input_type" => array("email" , "password"),
        "input_id" => array("inputEmail" , "inputPassword"),
        "input_class" => array("form-control " , "form-control"),
        "input_placeholder" => array("Email address" , "Password"),
        "input_required" => array("true" , "true"),
        "input_maxlength" => array("50 " , ""),
        "input_autofocus" => array("true " , "") 
    );
    $form_button_array = array(
        "button_href" => array(""),
        "button_class" => array("w-100 btn btn-lg btn-primary"),
        "button_type" => array("submit"),
        "button_name" => array("submit"),
        "button_value" => array("submit"),
        "button_text" => array("Sign in")
    );
    $form_inputs =  array(
        "form_input" => make_form_inputs($form_input_array),
        "button" => make_form_buttons($form_button_array),
        "input_seperator" => "mb-3",
        "button_seperator" => "mt-3"
    );
// website starts here:
make_head($language , $page_name , $css_path);
    echo("<body>");
        make_cdn_scripts();
        echo(make_navbar($nav_conf , $nav_links));
        echo(
            make_div( 
                $div_conf = "class=\"container body-height d-flex align-items-center justify-content-center\"",
                $print = 
                make_div(
                    $div_conf = "class=\"custom-login-height w-25\"" ,
                    $print =
                    make_div(
                        $div_conf = "" ,
                        make_form($form_config , $form_action , $form_method , $form_inputs)
                    )  
                ) 
            )
        );
    echo("
    </body>
</html>
");
?>