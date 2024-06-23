<?php

function validate_external_create_inputs($request , $pdo , &$error_message){
    printLog($request);
    if($request["origin"] === "reference"){
        if(!isset($request["user_id"])){
            $error_message = "User was not selected";
            return 0;
        }
        if(validate_user_in_db($request["user_id"] , $pdo) !== 1){
            $error_message = "User does not Exist";
            return 0;
        }
        if(isset($request["group_id"])){
            if(validate_group_in_db($request["group_id"] , $pdo) !== 1){
                $error_message = "group does not Exist";
                return 0;
            }
        }
    }
    if(isset($request["group"])){
        if(validate_external_inputs($request , "group" , " user_groups " , $pdo , $error_message) !== 1)
            return 0;
        if(intval($request["group"]["user_permission_level"]) < 0){
            $error_message = "Permission Level Invalid";
            return 0;
        }
        if(intval($request["group"]["status"]) < 0 || intval($request["group"]["status"]) > 1){
            $error_message = "Status Invalid";
            return 0;
        }
    }else{
        $error_message = "User didnt request any changes";
        return 0;
    }
    return 1;
}

function validate_external_update_inputs($request , $pdo , &$error_message){
    if(!isset($request["user_id"])){
        $error_message = "User was not selected";
        return 0;
    }
    if(validate_user_in_db($request["user_id"] , $pdo) !== 1){
        $error_message = "User does not Exist";
        return 0;
    }
    if(isset($request["group_id"])){
        if(validate_group_in_db($request["group_id"] , $pdo) !== 1){
            $error_message = "group does not Exist";
            return 0;
        }
    }
    if(!isset($data_request["group"])){
        $error_message = "User didnt request any changes";
        return 0;
    }
    if(isset($request["group"])){
        if(validate_external_inputs($request , "group" , " user_groups " , $pdo , $error_message) !== 1)
            return 0;
    }
    return 1;
}

function validate_external_delete_inputs($request , $pdo , &$error_message){
    $error_message = array();
    if(!isset($request["user_id"])){
        $error_message["unset_user"] = "Unset User Id";
        return 0;
    }
    if(!isset($request["equipment_id"])){
        $error_message["unset_id"] = "Unset Equipment Id";
        return 0;
    }
    if(validate_user_group_in_db($request["user_id"] , $request["group_id"] , $pdo) !== 1){
        $error_message["invalid_ids"] = "Invalid Selected User or Group";
        return 0;
    }
    if(validate_equipment_references($request , $pdo) !== 1){
        $error_message["invalid_ref"] = "Invalid Selected Equipment Reference";
        return 0;
    }
    $equipment_guard = validate_equipment_in_db($request["equipment_id"] , $pdo);
    switch($equipment_guard){
        case 1:
            return 1;
        //more than one user , or group is associated to this equipment
        case 2:
            return -1;
        //no equipment exists with the requested values
        case 0:
            $error_message["error"] = "Server Error";
            return -2;
        //thats weird the code didnt work properly 
        default:
            $error_message["error"] = "Server Error";
            return 0;
    }
    return 1;
}

function user_request_validation($tab){
    $trim_req = trim($tab);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "addusr":
            return 1;
        case "allusr":
            return 1;
        case "remusr":
            return 1;
        case "logusr":
            return 1;
        case "schusr":
            return 1;
        default:
            return 0;
    }
}

