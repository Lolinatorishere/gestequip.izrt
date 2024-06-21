<?php

function on_request_user_logs_load($data_request , $pdo){
    $ret = array();
    $logs = get_logs("user_logs" , $pdo);
    if($logs["total_items"] === 0)
        return array("Error" => "Error", "Server_Error" => "No Logs In the Database");
    return $logs;
}

function on_request_logs_refresh($data_request , $pdo){
    $log_type;
    switch($data_request["origin"]){
        case 'Error':
            $log_type = " Error ";
        case 'Warning':
            $log_type = " Warning ";
        case 'OK':
            $log_type = " OK ";
        default:
            return "invalid log type requested";
    }
    return get_logs_by_status($log_status , "user_logs" , $pdo);
}

function read_request_log($data_request , $pdo){
    if($_SESSION["user_type"] !== "Admin"){
        return array("Error" => "Error", "Auth_Error" => "Error");
    }
    // what queries can data specific have:
    //$data_specific = array("user" => array() ,"group_id" = "");
    if(!isset($data_request["refresh"])){
        return on_request_user_logs_load($data_request , $pdo);
    }else{
        return on_request_user_logs_refresh($data_request , $pdo);
    }
    
}


?>
