<?php
//  This is the configuration array for the navigation bar
//  The configuration array is used to generate the navigation bar
//
//  The configuration array has the following keys:
//  
//      nav_conf["links] is a boolean that determines if the navigation bar prints links or not
//          $nav_conf["links"] = true;
//  
//      nav_conf["current_link"] is a string that determines out of which printed links,
//      which one is the current link and meant to be highlighted.
//      $nav_conf["current_link"] = "Home";
//      
//    nav_links is an array that contains ul_class and li_items:
//    ul_class is a string that contains the classes for the ul tag
//    li_items is an array that contains the list items for the navigation bar
//
//    $nav_links = array(
//        "ul_class" => "navbar-nav /*aditional classes*/",
//        "li_items" => make_li_items($links) // the function make_li_items() is used to generate the navbar links
//    );

$nav_conf = [
    "navtag_conf" => "nav-height navbar navbar-expand-lg navbar-dark  bg-dark",
    "div_conf" => "container-fluid",
    "title_conf" => array(
        "title_class" => "navbar-brand nav-text-config",    
        "title_href" => "",
        "title_text" => "Gestor de Equipamentos"
    ),
    "profile_conf" => array(
        "profile_class" => "",
        "profile_pfp" => ""
    ),
];
?>