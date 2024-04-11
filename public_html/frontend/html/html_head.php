<?php
include __DIR__."/php_frontend_functions.php";
include __DIR__."/../../backend/session/check.php";

function make_head($language , $page_name , $csspath , $jspath){
    echo ("
    <!DOCTYPE html>
    <html lang=\"$language\">

    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>$page_name</title>");
        $csspath = convert_to_array($csspath);
        foreach($csspath as $path){
            echo("<link rel=\"stylesheet\" href=\"$path\">");
        }
        $jspath = convert_to_array($jspath);
        foreach($jspath as $path){
            echo("<script src=\"$path\"></script>");
        }
         
        echo("
        <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
        <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
        <link href=\"https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap\" rel=\"stylesheet\">  
        <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200\" />
         
    </head>
    ");
}
