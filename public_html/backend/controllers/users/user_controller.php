<?php
session_start();
// defines section 
// to avoid other files from working without the controller

// verifies if the user sending information is legit
if($_SESSION["user_type"] !== "Admin"){
    error_log(print_r($_SESSION , true));
    die;
}

//Inner Module Definitions
if(!defined('common_funcs'))
    define('common_funcs' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/common_functions.php');

if(!defined('pdo_config_dir'))
    define('pdo_config_dir' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');
 
if(!defined('query_generator_dir'))
    define('query_generator_dir' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/query_generators.php');

if(!defined('common_crud'))
    define('common_crud' , '/var/www/html/gestequip.izrt/public_html/backend/crud');

if(!defined('user_tabs'))
    define('user_tabs' , '/var/www/html/gestequip.izrt/public_html/backend/controllers/users/tabs');

//random modules
require_once "/var/www/html/gestequip.izrt/public_html/backend/common/merge_arrays.php"; 

//request modules
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/common/request_sanitize.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/common/request_validation.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/user/request.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/user/request_authentication.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/user/request_handling.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/user/request_sanitization.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/controllers/request/user/request_validation.php";

// crud modules 
require_once common_crud . "/common/describe_column.php";
require_once common_crud . "/create/user_create.php";
require_once common_crud . "/create/create_logs.php";
require_once common_crud . "/read/common_query.php";
require_once common_crud . "/read/group_query.php";
require_once common_crud . "/read/user_query.php";
require_once common_crud . "/read/search_query.php";
require_once common_crud . "/update/common_update.php";
require_once common_crud . "/update/user_update.php";
require_once common_crud . "/delete/user_delete.php";

// read tab modules
require_once user_tabs . "/add_user_tab.php";
require_once user_tabs . "/all_user_tab.php";
require_once user_tabs . "/logs_tab.php";
require_once user_tabs . "/remove_user_tab.php";
require_once user_tabs . "/search_tab.php";

// Base get requests 
$tab_request = $_GET["tab"];
$request_type = $_GET["type"];

// Base Post request
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $POST_RAW = file_get_contents('php://input');
    $parsed_data = json_decode($POST_RAW , true);
    foreach($parsed_data as $data){
        foreach ($data as $key => $value) {
            $_POST[$key] = $value;
        }
    }
}

// The controller
$req_tab = user_request_validation($tab_request);
$req_type = request_type_validation($request_type);
$ret = user_request_handle($req_tab , $tab_request , $req_type);
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
