<?php
function make_head($language , $page_name , $csspath){
    echo ("
    <!DOCTYPE html>
    <html lang=\"$language\">

    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>$page_name</title>
        <link rel=\"stylesheet\" href=\"$csspath\"
        <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
        <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
        <link href=\"https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap\" rel=\"stylesheet\">  
        <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200\" />
    </head>
    ");
}

function make_cdn_scripts(){
    echo("
        <script 
            src=\"https://code.jquery.com/jquery-3.3.1.slim.min.js\" 
            integrity=\"sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo\" 
            crossorigin=\"anonymous\">
        </script>
        <script
            src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js\" 
            integrity=\"sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1\" 
            crossorigin=\"anonymous\">
        </script>
        <script
            src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js\" 
            integrity=\"sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM\" 
            crossorigin=\"anonymous\">
        </script>
    ");
} 

