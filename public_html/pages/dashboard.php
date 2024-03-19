<?php
include_once __DIR__."/../frontend/html/html_head.php";
include_once __DIR__."/../frontend/html/navbar/html_nav.php";
include_once __DIR__."/../frontend/html/div_gen.php";

$language = "en";
$page_name = "Dashboard";
$css_path = "../frontend/css/dashboard.css";
$nav_title = ("<h1>
    Gestor de Equipamentos
</h1>"); 

$profile = ("
    <div class=\"profile\">
        <p>Username</p>
        <a href=\"#\">Logout</a>
    </div>
");
$navbar = array($nav_title , $profile);
make_head($language , $page_name , $css_path);
echo("<body>");
        echo(make_navbar($navbar));
        make_div(
            $div_conf = "class=\"\"",
            $div_content = "
                <div class=\"\""
        );
echo("</body>
</html>");
?>