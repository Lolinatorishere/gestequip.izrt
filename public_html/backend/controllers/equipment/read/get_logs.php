<?php

function default_equipment_logs($data_request , $pdo){
    $ret = array();
    $logs = get_logs("equipment_logs" , $pdo);
    if($logs["total_items"] === 0)
        return array("Error" => "Error", "Server_Error" => "No Logs In the Database");
    return $logs;
}

function specific_equipment_logs($data_request , $pdo){
    $log_status;
    $origin = trim($data_request["origin"]);
    switch($origin){
        case 'Error':
            $log_status = "Error";
            break;
        case 'Warning':
            $log_status = "Warning";
            break;
        case 'OK':
            $log_status = "OK";
            break;
        default:
            return "invalid log type requested";
    }
    return get_logs_by_status($log_status , "equipment_logs" , $pdo);
}

function read_request_log($data_request , $pdo){
    if($_SESSION["user_type"] !== "Admin"){
        return array("Error" => "Error", "Auth_Error" => "Error");
    }
    // what queries can data specific have:
    //$data_specific = array("user" => array() ,"group_id" = "");
    if(!isset($data_request["refresh"])){
        return default_equipment_logs($data_request , $pdo);
    }else{
        return specific_equipment_logs($data_request , $pdo);
    }
}

?>
