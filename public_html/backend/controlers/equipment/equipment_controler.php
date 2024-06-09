<?php
session_start();
// defines section 
// to avoid other files from working without the controler

if(!defined('common_funcs'))
    define('common_funcs' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/common_functions.php');

if(!defined('pdo_config_dir'))
    define('pdo_config_dir' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');
 
if(!defined('query_generator_dir'))
    define('query_generator_dir' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/query_generator.php');

if(!defined('query_generator_dir'))
    define('common_crud' , '/var/www/html/gestequip.izrt/public_html/backend/crud');

require_once "/var/www/html/gestequip.izrt/public_html/backend/common/merge_arrays.php"; 

// crud functions 
require_once common_crud . "/common/describe_column.php";
require_once common_crud . "/delete/equipment_delete.php";
require_once common_crud . "/create/equipment_create.php";
require_once common_crud . "/create/create_logs.php";
require_once common_crud . "/update/equipment_update.php";
require_once common_crud . "/read/common_query.php";
require_once common_crud . "/read/equipment_query.php";
require_once common_crud . "/read/group_query.php";
require_once common_crud . "/read/user_query.php";

// Post request
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $POST_RAW = file_get_contents('php://input');
    $parsed_data = json_decode($POST_RAW , true);
    foreach($parsed_data as $data){
        foreach ($data as $key => $value) {
            $_POST[$key] = $value;
        }
    }
}

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

function type_validation($type){
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

function load_ui($ui){
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

// checks if the get request is even valid
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

function is_user_in_groups($groups){
    $guard = 0;
    $group_auth = array('auth' => 1 
                       ,'own_auth' => 1
                       ,'de_auth' => 1
    );
    foreach ($group_auth as $key => $value) {
        if(count($groups[$key]) != 0){
            $guard++;
        }
    }
    if($guard == 0){
        return 1;
    }
    return 0;
}

function equipment_group_user_sql_metacode($queried_users){
    $unique_users = array();
    $sql = '';
    $i = 1;
    foreach($queried_users as $user){
        array_push($unique_users , $user["user_id"]);
    }
    $unique_users = array_unique($unique_users);
    $total = count($unique_users);
    foreach($unique_users as $user){
        $sql .= $user;
        if($i < $total){
            $sql .= ', ';
        }
        $i++;
    }
    return $sql;
}

// bloody hell this was a headache and a half to write
function equipment_sql_query_metacode($user_data){
    $sql = '';
    $groups = array();
    $i = 0;
    foreach($user_data as $info){
        array_push($groups , $info["group_id"]);
    }
    $groups = array_unique($groups);
    $total_groups = count($groups);
    foreach($groups as $group){
        $sql .= "( group_id = " . $group ;
        $sql_users = '';
        $total_users = 0;
        $user_array = array();
        foreach($user_data as $info){
            if($info["group_id"] === $group){
                array_push($user_array , $info["user_id"]);
            }
        }
        $user_array = array_unique($user_array);
        $total_users = count($user_array);
        for($j = 0 ; $j < $total_users ; $j++){
            $sql_users .= $user_array[$j];
            if($j+1 !== $total_users){
                $sql_users .= ", ";    
            }
        }
        if($total_users !== 0){
            $sql .= ' AND user_id IN ( ' . $sql_users . ' ) ';
        }else{
            $sql .= ' AND user_id = 0 ';
        }
        $sql .= ' ) ';
        if($i+1 !== $total_groups){
            $sql .= " OR ";
        }
        $i++;
    }
    return $sql;
}

function sql_array_query_metacode($inputs){
    $sql = '';
    $i = 1;
    $total = count($inputs);
    foreach($inputs as $input){
        $sql .= $input;
        if($i !== $total){
            $sql .= ', ';
        } 
    }
    return $sql;
}

function custom_query_filter($number){
    $filter = array("filter" => array());
    for($i = 0 ; $i < $number/2 ; $i++){
        $filter["filter"][$i] = $i;
    }
    return $filter;
}


function parse_equipment_type_columns($columns){
    $parsed_columns = array();
    $i = 1;
    $filter = custom_query_filter(count($columns));
    foreach($columns as $column){
        if($column["Key"] === "PRI")
            continue;
        if($column["Key"] === "MUL")
            continue;
        if(isset($column["Default"]))
            continue;
        if($column["Field"] === "id")
            continue;
        if($column["Field"] === "equipment_id")
            continue;
        if($column["Field"] === "registration_date")
            continue;
        if($column["Field"] === "registration_lock")
            continue;
        if($column["Field"] === "equipment_type")
            continue;
        if($column["Field"] === "serial_md5")
            continue;
        $column = merge_arrays($filter , $column);
        array_push($parsed_columns , $column);
    }
    return $parsed_columns;
}

function clean_query($filter , $items){
    $washed_items = array();
    foreach($items as $item){
        array_push($washed_items , merge_arrays($filter , $item));
    } 
    return $washed_items;
}

function read_request_sch($data_request , $pdo , $user_id){
    
}

function tab_read_request($tab , &$data_request , $user_id , $pdo){
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
            return;
        case 'add_eq':
            return read_request_add($data_request , $pdo , $user_id);
        }
    $data_request["error"] = "error";
    return $data_request;
}


function tab_create_request($data_request , $tab , $user_id , $pdo){
    switch($tab){
        case "add_eq":
            return create_equipment($data_request , $pdo);
    }
}

function tab_create_information($tab , $user_id , $pdo){
    if(!isset($_POST["selected_group"]))
        return 0;
    if(!isset($_POST["selected_user"]))
        return 0;
    if(!isset($_POST["equipment_type"]))
        return 0;
    if(!isset($_POST["default"]))
        return 0;
    if(!isset($_POST["specific"]))
        return 0;
    $equipment_type = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["equipment_type"]); 
    $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_user"]["user_id"]);
    $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_group"]["group_id"]);
    $data_request["equipment_type"] = $equipment_type; 
    $default_info = $_POST["default"];
    $specific_info = $_POST["specific"];
    foreach($default_info as $key => $info){
        $data_request["default"][$key] =  $info;
    }
    foreach($specific_info as $key => $info){
        $data_request["specific"][$key] =  $info;
    }
    return tab_create_request($data_request , $tab , $user_id , $pdo);
}

// gets the correct requests for each tab
function tab_read_information($tab , $user_id , $pdo){
    $data_request = array();
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
    if(isset($_GET["rfsh"])){// refesh x data
        $refresh = preg_replace('/[^a-zA-Z_]/s' , '' , $_GET["rfsh"]); 
        $data_request["refresh"] = $refresh;
    }
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]); 
        $data_request["origin"] = $origin;
    }
    return tab_read_request($tab , $data_request , $user_id , $pdo);
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
    if($crud === 1) //create new info
        $data = tab_create_information($tab , $user_id , $pdo);
    if($crud === 2) //read info
        $data = tab_read_information($tab , $user_id , $pdo);
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
    echo json_encode(array('data' => $ret[0]
                          ,'tab' => $tab_request
                          ,'information' => $ret[1]));
}
unset($pdo);
unset($_POST);
