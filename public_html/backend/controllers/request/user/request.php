<?php

if($_SESSION["user_type"] !== "Admin"){
    die;
}

function ui_request($ui){
    $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/user/tabs/';
    if(ui_refresh_origin() == 1){
        $ui_dir = preg_replace('/[^a-zA-Z\/_]/s' , '' , $_GET["rfsh"]);
        $dir .= $ui_dir . '.html';
        return file_get_contents($dir);
    }
    $dir .= $ui . '.html';
    return file_get_contents($dir);
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
        case 2: //Read request
            return read_information_sanitize($tab , $user_id , $pdo);
        case 3: //Update request
            return update_information_sanitize($tab , $user_id , $pdo);
        case 4: //Delete request
            return delete_information_sanitize($tab , $user_id , $pdo);
        default:
            return $ret;
    }
}

function read_request($tab , &$data_request , $user_id , $pdo){
    $data = array();
    switch($tab){
        case "allusr":
            return read_all_user($data_request , $pdo);
        case "grpusr":
            return read_group_users($data_request , $pdo);
        case "schusr":
            return read_request_search($data_request , $pdo);
        case 'getlog':
            return read_request_log($data_request , $pdo);
        }
    $data_request["error"] = "error";
    return $data_request;
}

function create_request($data_request , $tab , $user_id , $pdo){
    switch($origin) {
        case 'user':
            return create_user($data_request , $pdo);
        case 'reference':
            $data_request["reference"] = "user_group";
            return create_reference($data_request , $pdo);
        default:
            return "Server Error";
    }
}

function update_request($data_request , $tab , $user_id , $pdo , $origin){
    switch($origin){
        case 'user':
            return update_user($data_request , $pdo);
        case 'permission':
            return update_user_group_permission($data_request , $pdo);
        default:
            return "Origin Error";
    }
}

function delete_request($data_request , $tab , $user_id , $pdo , $origin){
    switch($origin) {
        case 'user':
            return delete_user($data_request , $pdo);
        case 'reference':
            $data_request["reference"] = "user_group";
            return delete_reference($data_request , $pdo);
        default:
            return "Server Error";
    }
}

?>
