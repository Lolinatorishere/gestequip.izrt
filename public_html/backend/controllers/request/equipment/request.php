<?php

function read_request($tab , &$data_request , $user_id , $pdo){
    $data = array();
    switch($tab){
        case "speceq":
            return read_equipment($data_request , $pdo);
        case "eqtype":
            return read_equipment_types($pdo);
        case "youreq":
            return read_your_equipment($data_request , $pdo);
        case "tbdesc":
            return read_table_description($data_request , $pdo);
        case "autheq":
            return read_authorised_equipment($data_request , $pdo);
        case "all_eq":
            return read_all_equipment($data_request , $pdo);
        case "sch_eq":
            return read_searched_query($data_request , $pdo);
        case "getlog":
            return read_request_log($data_request , $pdo);
        }
    $data_request["error"] = "error";
    return $data_request;
}

function create_request($data_request , $tab , $user_id , $pdo){
    switch($tab){
        case "add_eq":
            return create_equipment($data_request , $pdo);
    }
    $data_request["error"] = "error";
    return $data_request;
}

function update_request($data_request , $tab , $user_id , $pdo){
    return update_equipment($data_request , $pdo);
}

function delete_request($data_request , $tab , $user_id , $pdo){
    return delete_equipment($data_request , $pdo);
}

// function to handle crud requests from tabs
function data_request($tab , $pdo , $user_id){
    //default return value
    $ret = array(
        'success' => 'false');
    if(!isset($_GET["crud"]))
        return $ret;
    $crud = request_crud_validation();
    switch($crud){
        case 0:
            return $ret;
        case 1: //Create request
            return create_information_sanitize($tab , $user_id , $pdo);
        case 2: //Reat request
            return read_information_sanitize($tab , $user_id , $pdo);
        case 3: //Update request
            return update_information_sanitize($tab , $user_id , $pdo);
        case 4: //Delete request
            return delete_information_sanitize($tab , $user_id , $pdo);
        default:
            return $ret;
    }
}

function ui_request($ui){
    $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/equipment/tabs/';
    if(ui_refresh_origin() == 1){
        $ui_dir = preg_replace('/[^a-zA-Z\/_]/s' , '' , $_GET["rfsh"]);
        $ui_dir3 = preg_replace('/[^a-zA-Z\/_]/s' , '' , $_GET["rgin"]);
        $dir .= $ui_dir . '.html';
        return file_get_contents($dir);
    }
    $dir .= $ui . '.html';
    return file_get_contents($dir);
} 

?>
