<?php

function validate_create_table_inputs($request , $check , $db_table , $pdo){
    $table_check = 0;
    $table_request = array("table" => $db_table);
    $table = describe_table($table_request , $pdo);
    $counted_table = count($request[$check]);
    foreach($request[$check] as $key => $value){
        for ($i = 0; $i < count($table["items"]) ; $i++){ 
            try{
                if($table["items"][$i]["Field"] !== $key)
                    continue;
                if(preg_match('/[<>\'`\/\\\\_]/' , $request[$check][$key]))
                    continue;
                if($table["items"][$i]["Key"] === "UNI"){
                    $unique_request = array("fetch" => " " . $key . " "
                                           ,"table" => $db_table
                                           ,"counted" => 1
                                           ,"specific" => " " . $key . "='" . $value . "'"
                                        );
                    $unique = get_queries($unique_request , $pdo);
                    if(count($unique["items"]) >= 1)
                        return 0;
                }
                if($table["items"][$i]["Null"] === "NO"){
                    if(is_null($value))
                        return 0;
                }
                if($table["items"][$i]["Type"] === "tinyint(1)"){
                    if($request[$check][$key] !== false && $request[$check][$key] !== true)
                        return 0;
                }
                if($table["items"][$i]["Type"] === "date"){
                    list($year , $month , $day) = explode('-', $request[$check][$key]);
                    if(!checkdate($month , $day , $year))
                        return 0;
                }
            }catch(TypeError $e){
                error_log(print_r($e , true));
                return 0;
            }
            $table_check++;
        }
    }
    if($table_check !== $counted_table)
        return 0;
    return 1;
}

function equipment_create_request_validation($request , $pdo){
    if(!isset($request["default"]))
        return 0;
    if(!isset($request["specific"]))
        return 0;
    if(!isset($request["user_id"]))
        return 0;
    if(!isset($request["group_id"]))
        return 0;
    if(!isset($request["group_id"]))
        return 0;
    if(validate_create_table_inputs($request , "default" , " equipment " , $pdo) === 0)
        return 0;
    if(validate_create_table_inputs($request , "specific" , " " . $request["equipment_type"] . "s " , $pdo) === 0)
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
