<html>
<?php
    $language = "en";
    $page_name = "hello world!";
    $csspath = "/frontend/css/index.css";
    include __dir__."/frontend/html/htmlhead.php";
    include __dir__."/frontend/html/htmlnav.php";
?>
<?php
    $nav_conf = [
        "navtag_conf" => "navbar navbar-expand-lg navbar-dark fixed-top bg-dark",
        "div_conf" => "container-fluid ",
        "title_conf" => array(
            "title_class" => "navbar-brand mt-2 mt-lg-2 mb-2 mb-lg-2",
            "title_href" => "#",
            "title_text" => "Gestor de Equipamentos"
        )
    ];
    $nav_links = array(
        "ul_class" => "navbar-nav me-auto mb-2 mb-md-0",
        "li_items" => array(
            array(
                "li_class" => "nav-item",
                "a_class" => "nav-link active",
                "a_href" => "#",
                "a_text" => "Home"
            ),
            array(
                "li_class" => "nav-item",
                "a_class" => "nav-link",
                "a_href" => "#",
                "a_text" => "Link"
            ),
            array(
                "li_class" => "nav-item",
                "a_class" => "nav-link",
                "a_href" => "#",
                "a_text" => "Disabled"
            )
        )
    );
    make_navbar($nav_conf , $nav_links);

?>
</body>
</html>