<?php

// checks if the request is even valid
function request_crud_validation(){
    switch($_GET["crud"]){
        case "create":
            return 1;
        case "read":
            return 2;
        case "update":
            return 3;
        case "delete":
            return 4;
        default:
            return 0;
    }
}

function ui_refresh_origin(){
    if(!isset($_GET["rfsh"]))
        return 0;
    if(!isset($_GET["rgin"]))
        return 0;
    if($_GET["rfsh"] === "undefined")
        return 0;
    if($_GET["rgin"] === "undefined")
        return 0;
    return 1;
}

function equipment_request_validation($tab){
    $trim_req = trim($tab);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "yur_eq":
            return 1;
        case "grp_eq":
            return 1;
        case "sch_eq":
            return 1;
        case "add_eq":
            return 2;
        case "all_eq":
            return 2;
        case "rem_eq":
            return 2;
        case "adeqty":
            return 3;
        case "log_eq":
            return 3;
        default:
            return 0;
    }
}

function equipment_type_validation($type){
    $trim_type = trim($type);
    if(strlen($trim_type) !== 4){
        return 0;
    }
    switch($type){
        //user interface requested
        case "usri":
            return 1;
        //tab data requested
        case "data":
            return 2;
        default:
            return 0;
    }
}
?>
