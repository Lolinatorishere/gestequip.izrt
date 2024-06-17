<?php

// Validate table inputs
// uses the requests and checks against the db table to see if its a valid search
function validate_external_search_inputs($request , $check , $db_table , $pdo){
    $table_check = 0;
    $table_request = array("table" => $db_table);
    $table = describe_table($table_request , $pdo);
    $counted_table = count($request[$check]);
    foreach($request[$check] as $key => $value){
        for ($i = 0; $i < count($table["items"]) ; $i++){ 
            if($table["items"][$i]["Field"] !== $key)
                continue;
            if($table["items"][$i]["Key"] === "PRI")
                continue;
            if($table["items"][$i]["Key"] === "MUL")
                continue;
            $table_check++;
        }
    }
    if($table_check !== $counted_table)
        return 0;
    return 1;
}

function validate_external_inputs($request , $check , $db_table , $pdo , &$error_message){
    $table_check = 0;
    $table_request = array("table" => $db_table);
    $table = describe_table($table_request , $pdo);
    $counted_table = count($request[$check]);
    foreach($request[$check] as $key => $value){
        for ($i = 0; $i < count($table["items"]) ; $i++){ 
            try{
                if($table["items"][$i]["Field"] !== $key){
                    continue;
                }
                if(preg_match('/[<>\'`\/\\\\_]/' , $request[$check][$key])){
                    $error_message[$key] =  $key . " invalid Inputs";
                    continue;
                }
                if($table["items"][$i]["Key"] === "UNI"){
                    $unique_request = array("fetch" => " " . $key . " "
                                           ,"table" => $db_table
                                           ,"counted" => 1
                                           ,"specific" => " `" . $key . "`='" . $value . "'"
                                        );
                    $unique = get_queries($unique_request , $pdo);
                    if(count($unique["items"]) >= 1){
                        $error_message[$key] = $key . " is not unique";
                        throw new Exception($error_message[$key], 1);
                    }
                }
                if($table["items"][$i]["Null"] === "NO"){
                    if(is_null($value)){
                        $error_message[$key] =  $key . " can't be empty";
                        throw new Exception($error_message[$key], 1);
                    }
                }
                if($table["items"][$i]["Type"] === "tinyint(1)"){
                    if($request[$check][$key] !== false && $request[$check][$key] !== true){
                        $error_message[$key] =  $key . " invalid choice";
                        throw new Exception($error_message[$key] , 1);
                    }
                }
                if($table["items"][$i]["Type"] === "date"){
                try{
                    list($year , $month , $day) = explode('-', $request[$check][$key]);
                    if(!checkdate($month , $day , $year)){
                        $error_message[$key] =  $key . " invalid date";
                        throw new Exception($error_message[$key] , 1);
                    }
                }catch(typeError $e){
                    $error_message[$key] =  $key . " invalid date";
                    throw new Exception($error_message[$key] , 1);
                }
                }
            }catch(exception $e){
                error_log(print_r($e , true));
                return 0;
            }
            $table_check++;
        }
    }
    if($table_check !== $counted_table){
        $error_message["Missmatch"] = " invalid inputs tables sent";
        return 0;
    }
    return 1;
}

function validate_equipment_in_db($equipment_id , $pdo){
    $request = array("fetch" => " * " 
                    ,"table" => " users_inside_groups_equipments "
                    ,"specific" => " equipment_id=\"" . $equipment_id ."\""
                    );
    $equipment = get_queries($request , $pdo);
    if($equipment["total_items"] !== 1)
        return 0;
    return 1;
}

function validate_user_group_in_db($user_id , $group_id , $pdo){
    $request = array("fetch" => " * " 
                    ,"table" => " users_inside_groups"
                    ,"specific" => " group_id=\"" . $group_id ."\""
                                 . " AND "
                                 . " user_id= \"" . $user_id . "\""
                    );
    $equipment = get_queries($request , $pdo);
    if($equipment["total_items"] !== 1)
        return 0;
    return 1;
}

function validate_external_update_inputs($request , $pdo , &$error_message){
    if(!isset($request["equipment_id"])){
        $error_message["eq_not_selected"] = "User didnt Select an Equipment";
        return -1;
    }
    $error_message["eq_not_exists"] = "User Selected an Equipment that Doesnt Exist";
    if(validate_equipment_in_db($request["equipment_id"] , $pdo) !== 1){
        return -2;
    }
    if(isset($request["default"])){
        if(validate_external_inputs($request , "default" , " equipment " , $pdo , $error_message) !== 1)
            return -3;
        
    }
    if(isset($request["specific"])){
        if(validate_external_inputs($request , "specific" , " " . $request["equipment_type"] . " " , $pdo , $error_message) !== 1)
            return -4;
    }
    if(!isset($request["default"]) && (!isset($request["specific"]))){
        $error_message["No_query"] = "No Queries have been input";
        return -5;
    }
    return 1;
}

function validate_external_create_inputs($request , $pdo , &$error_message){
    $error_message = array();
    if(!isset($request["default"]) || !isset($request["specific"])){
        $error_message["unset_input"] = "Unset Inputs";
        return -1;
    }
    if(!isset($request["user_id"]) || !isset($request["group_id"])){
        $error_message["unset_id"] = "Unset Ids";
        return -2;
    }
    if(validate_user_group_in_db($request["user_id"] , $request["group_id"] , $pdo) !== 1){
        $error_message["invalid_ids"] = "Invalid Selected User or Group";
        return -3;
    }
    if(validate_external_inputs($request , "default" , " equipment " , $pdo , $error_message) !== 1)
        return -4;
    if(validate_external_inputs($request , "specific" , " " . $request["equipment_type"] , $pdo , $error_message) !== 1)
        return -5;
    return 1;
}

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
