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
                try{
                    if(preg_match('/^date.*' , $table["items"][$i]["Type"])){
                        list($year , $month , $day) = explode('-', $request[$check][$key]);
                        if(!checkdate($month , $day , $year)){
                            $error_message[$key] =  $key . " invalid date";
                            throw new Exception($error_message[$key] , 1);
                        }
                    }
                }catch(typeError $e){
                    $error_message[$key] =  $key . " invalid date";
                    throw new Exception($error_message[$key] , 1);
                }
            }catch(exception $e){
                error_log(print_r($e , true));
                return 0;
            }
            $table_check++;
        }
    }
    if($table_check !== $counted_table){
        $error_message["Missmatch"] = " invalid inputs received";
        return 0;
    }
    return 1;
}

function validate_equipment_references($data_request , $pdo){
    $request = array("fetch" => " * " 
                    ,"table" => " users_inside_groups_equipments "
                    ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                . " AND group_id=" . $data_request["group_id"]
                                . " AND user_id=" . $data_request["user_id"]
                    );
    $equipment = get_queries($request , $pdo);
    if($equipment["total_items"] != 1){
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
    if($equipment["total_items"] === 1)
        return 1;
    if($equipment["total_items"] > 1)
        return 2;
    if($equipment["total_items"] === 0)
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

function request_type_validation($type){
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

?>
