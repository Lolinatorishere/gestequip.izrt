<?php

function read_request($tab , &$data_request , $user_id , $pdo){
    $group_ids = '';
    $user_ids = '';
    $equipment_ids ='';
    $data = array();
    switch($tab){
        case "yur_eq":
            return read_request_yur($data_request , $pdo , $user_id);
        case "grp_eq":
            return read_request_grp($data_request , $pdo , $user_id);
        case "sch_eq":
            return read_request_sch($data_request , $pdo , $user_id);
        case 'add_eq':
            return read_request_add($data_request , $pdo , $user_id);
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

function update_request(){
    switch($tab){
        case "add_eq":
            return update_equipment($data_request , $pdo);
        case "grp_eq":
            return update_equipment($data_request , $pdo);
    }
    $data_request["error"] = "error";
    return $data_request;
}

function delete_request(){
    $data_request["error"] = "error";
    return $data_request;
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
            return tab_create_information_sanitize($tab , $user_id , $pdo);
        case 2: //Reat request
            return tab_read_information_sanitize($tab , $user_id , $pdo);
        case 3: //Update request
            return tab_update_information_sanitize($tab , $user_id , $pdo);
        case 4: //Delete request
            return tab_delete_information_sanitize($tab , $user_id , $pdo);
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
