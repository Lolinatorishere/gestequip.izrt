<?php

function validate_external_create_inputs($request , $pdo , &$error_message){
    if(!isset($request["group"])){
        $error_message = "User didn't Provide Valid Input";
        return 0;
    }
    if(validate_external_inputs($request , "group" , " user_groups " , $pdo , $error_message) !== 1)
        return 0;
    if(intval($request["group"]["status"]) < 0 || intval($request["group"]["status"]) > 1){
        $error_message = "Status Invalid";
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
    if(!isset($request["group_id"])){
        $error_message["unset_group"] = "Group was Not Set";
        return 0;
    }
    if(intval($request["group_id"]) <= 0){
        $error_message["invalid_group"] = "Invalid Selected Group";
        return 0;
    }
    if(validate_group_in_db($request["group_id"] , $pdo) !== 1){
        $error_message["group_not_exist"] = "Selected Group Non Existent";
        return 0;
    }else{
        if($request["group_id"] === "1"){
            $error_message["invalid_group"] = "You Can NOT Delete this Group";
            return 0;
        }
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

