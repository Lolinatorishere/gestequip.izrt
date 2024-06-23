<?php

function validate_external_create_inputs($request , $pdo , &$error_message){
    if(!isset($request["virtual"])){
        $error_message["invalid_inputs"] = "Incomplete Request";
        return 0;
    }
    if(!isset($request["email"])){
        $error_message["unset_email"];
    }
    if(!filter_var($request["email"] , FILTER_VALIDATE_EMAIL)){
        $error_message["invalid_email"] = "Invalid Email Inserted";
        $error_message["email"] = $request["email"];
        return 0;
    }
    if(!isset($request["user"]["pass"])){
        if($request["virtual"] !== "1"){
            $error_message["unset_password"] = "The users password has not been set";
            return 0;
        }
    }
    if(validate_external_inputs($request , "user" , " users " , $pdo , $error_message) !== 1)
        return 0;
    if(validate_full_input($request , "user" , " users " , $pdo , $error_message) !== 1)
        return 0;
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
    if(isset($request["user"]["date_created"])){
        unset($request["user"]["date_created"]);
    }
    if(isset($request["user"])){
        if(validate_external_inputs($request , "user" , " users " , $pdo , $error_message) !== 1)
            return 0;
    }else{
        $error_message = "User didnt request any changes";
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

