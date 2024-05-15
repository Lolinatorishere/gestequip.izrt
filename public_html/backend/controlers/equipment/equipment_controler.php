<?php
session_start();
// defines section 
// to avoid other files from working without the controler
if(!defined('pdo_config_dir'))
    define('pdo_config_dir' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');

if(!defined('sql_queries_dir'))
    define('sql_read_queries_dir' , '/var/www/html/gestequip.izrt/public_html/backend/crud/sql_queries/read_queries.php');

// authentication code
include_once "/var/www/html/gestequip.izrt/public_html/backend/auth/user_auth.php";
include_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/equipment_query.php";
include_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/user_query.php";

// base get requests 
$tab_request = $_GET["tab"];
$request_type = $_GET["type"];
function tab_request_validation($tab){
    $trim_req = trim($tab);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "yur_eq":
            return 1;
            break;
        case "grp_eq":
            return 1;
            break;
        case "sch_eq":
            return 1;
            break;
        case "add_eq":
            return 2;
            break;
        case "all_eq":
            return 2;
            break;
        case "rem_eq":
            return 2;
            break;
        case "log_eq":
            return 3;
            break;
        default:
            return 0;
            break;
    }
}

function type_validation($type){
    $trim_type = trim($type);
    if(strlen($trim_type) !== 4){
        return 0;
    }
    switch($type){
        //user interface requested
        case "usri":
            return 1;
            break;
        //tab data requested
        case "data":
            return 2;
            break;
        default:
            return 0;
            break;
    }
}

function load_ui($ui){
    $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/equipment/tabs/' . $ui . '.html';
    return file_get_contents($dir);
} 

// checks if the get request is even valid
function request_crud_validation(){
    switch($_GET["crud"]){
        case "create":
            return 1;
            break;
        case "read":
            return 2;
            break;
        case "update":
            return 3;
            break;
        case "delete":
            return 4;
            break;
        default:
            return 0;
            break;
    }
}

function request_crud_authentication($pdo , $user_id){
    $group_permissions = user_group_auth($user_id , $pdo);
    // if admin do as you wish
    if($_SESSION["user_type"] === "Admin")
        return array("admin" => "true");
    $group_permissions;
}

function set_tab_request($tab , &$data_request , $user_id){
    $request = array();
    $group_ids = '';
    switch($tab){
        case "yur_eq":
            $request["fetch"] = " * ";
            $request["specific"] = "user_id = " . $user_id;
            $request["table"] = "users_inside_groups_equipments";
            $data_request = $request;
            return;
        case "grp_eq":
            $request["fetch"] = " * ";
            $request["specific"] = "user_id = " . $user_id;
            $request["table"] = "users_inside_groups";
            $request["counted"] = 1;
            $group_info = get_user_groups($request);
            for($i = 0 ; $i < $group_info["total_items"] ; $i++){
                $group_ids .= $group_info["all_groups"][$i];
                if($i !== $group_info["total_items"]-1)
                    $group_ids .= ', ';
            }
            $request = array();
            $request["fetch"] = " equipment_id , group_id ";
            $request["specific"] = "group_id IN ( " . $group_ids . " ) "; 
            $request["table"] = "users_inside_groups_equipments";
            $request["counted"] = 1;
            $all_equipment = get_group_equipments($request);
            error_log(print_r($all_equipment , true));
            return;
    }
    $data_request["error"] = "error";
    return;
}

// gets the correct requests for each tab
function tab_fetch_data($tab , $user_id){
    $data_request = array();
    set_tab_request($tab , $data_request , $user_id);
    if(isset($_GET["page"])){
        $page = preg_replace('/[^0-9]/s' , '' , $_GET["page"]); 
        $data_request["page"] = $page;
    }
    if(isset($_GET["t_i"])){
        $total_items = preg_replace('/[^0-9]/s' , '' , $_GET["t_i"]); 
        $data_request["total_items"] = $total_items;
    }
    if(isset($_GET["pgng"])){
        $paging = preg_replace('/[^0-9]/s' , '' , $_GET["pgng"]); 
        $data_request["paging"] = $paging;
    }
    $equipment = get_equipments($data_request);
    if(isset($data_request["multiple"])){

    }
    return $equipment;
}


// function to handle crud requests from tabs
function data_request($tab , $pdo , $user_id){
    //default return value
    $ret = array(
        'success' => 'false');
    if(!isset($_GET["crud"]))
        return $ret;
    $crud = request_crud_validation();
    if($crud === 0)
        return $ret;
    if($crud === 2) //read info
        $data = tab_fetch_data($tab , $user_id);
    
    else{
        $auth = request_crud_authentication($pdo , $user_id);
    }
    unset($pdo);
    $ret = $data;
    return $ret;
}

function tab_auth_handle($auth_level){
    if($auth_level === 1)
        return 1;
    if($_SESSION["user_type"] === 'Admin')
        return 1;
    if($auth_level === 2){
        if($_SESSION["user_type"] === 'Manager')
            return 1;
    }
    return 0;
} 

function request_handle($tab_valid , $tab , $type_valid){
    // validity guard clause
    if($tab_valid === 0)
        return array("error");
    // auth guard clause
    if(tab_auth_handle($tab_valid) === 0)
        return array("error");
    // checks if the type_valid is equivilent to data
    if($type_valid === 2){
        require_once pdo_config_dir;
        $user_id = $_SESSION["id"];
        $ret = data_request($tab , $pdo , $user_id);
        // equipment_parser($ret);
        unset($pdo);
        if(isset($ret["error"]))
            return array("unavailable" , $ret);
        return array("success" , $ret);
    }
    return array("success" , load_ui($tab));
}

$req_tab = tab_request_validation($tab_request);
$req_type = type_validation($request_type);
$ret = request_handle($req_tab , $tab_request , $req_type);

if($req_type === 1){
    echo json_encode(array('ui' => $ret[0]
                          ,'html' => $ret[1]));
}
if($req_type === 2){
    error_log(print_r($ret[0] , true));
    echo json_encode(array('data' => $ret[0]
                          ,'tab' => $tab_request
                          ,'information' => $ret[1]));
unset($pdo);
}
?>