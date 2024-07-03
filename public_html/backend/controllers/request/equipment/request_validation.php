<?php
 
function validate_external_create_inputs($request , $pdo , &$error_message){
    $error_message = array();
    if(!isset($request["default"]) || !isset($request["specific"])){
        $error_message["unset_input"] = "Unset Inputs";
        return 0;
    }
    if(!isset($request["equipment_type"])){
        $error_message["unset_equipment_type"] = "Unset Equipment Type";
        return 0;
    }
    if(validate_user_group_in_db($request["user_id"] , $request["group_id"] , $pdo) !== 1){
        $error_message["invalid_ids"] = "Invalid Selected User or Group";
        return 0;
    }
    if(validate_external_inputs($request , "default" , " equipment " , $pdo , $error_message) !== 1){
        return 0;
    }
    if(validate_external_inputs($request , "specific" , " " . $request["equipment_type"] , $pdo , $error_message) !== 1)
        return 0;
    if(validate_full_input($request , "default" , " equipment " , $pdo , $error_message) !== 1)
        return 0;
    if(validate_full_input($request , "specific" , " " . $request["equipment_type"] , $pdo , $error_message) !== 1)
        return 0;
    return 1;
}

function validate_external_request_inputs($request , $pdo , &$error_message){
    $error_message = array();
    if(!isset($request["user_id"]) || !isset($request["group_id"]) || !isset($request["equipment_id"])){
        $error_message["unset_id"] = "unset ids";
        return 0;
    }
    
    if(validate_user_group_in_db($request["user_id"] , $request["group_id"] , $pdo) !== 1){
        $error_message["invalid_ids"] = "invalid selected user or group";
        return 0;
    }
    if(validate_external_inputs($request , "default" , " equipment " , $pdo , $error_message) !== 1)
        return 0;
    if(validate_external_inputs($request , "specific" , " " . $request["equipment_type"] , $pdo , $error_message) !== 1)
        return 0;
    if(validate_full_input($request , "default" , " equipment " , $pdo , $error_message) !== 1)
        return 0;
    if(validate_full_input($request , "specific" , " " . $request["equipment_type"] , $pdo , $error_message) !== 1)
        return 0;
    return 1;
}

function validate_external_update_inputs($request , $pdo , &$error_message){
    if(!isset($request["equipment_id"])){
        $error_message["eq_not_selected"] = "User didnt Select an Equipment";
        return 0;
    }
    if(validate_equipment_in_db($request["equipment_id"] , $pdo) === 0){
        $error_message["eq_not_exists"] = "User Selected an Equipment that Doesnt Exist";
        return 0;
    }
    if(validate_user_group_in_db($request["user_id"] , $request["group_id"] , $pdo) !== 1){
        $error_message["user_not_exists"] = "User Selected a User That doesnt Exist, or is not part of the selected group";
        return 0;
    }
    if(validate_equipment_references($request , $pdo) !== 1){
        $error_message["invalid_ref"] = "Invalid Selected Equipment Reference";
        return 0;
    }
    if(isset($request["default"])){
        if(validate_external_inputs($request , "default" , " equipment " , $pdo , $error_message) !== 1)
            return 0;
    }
    if(isset($request["specific"])){
        if(validate_external_inputs($request , "specific" , " " . $request["equipment_type"] . " " , $pdo , $error_message) !== 1)
            return 0;
    }
    if(!isset($request["default"]) && !isset($request["specific"])){
        $error_message["No_query"] = "No Queries have been input";
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
    if(!isset($request["group_id"])){
        $error_message["unset_group"] = "Unset Group Id";
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
    $equipment_guard = validate_equipment_references_in_db($request["equipment_id"] , $pdo);
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

function validate_equipment_delete($request , $pdo , &$error_message){

}

function equipment_request_validation($tab){
    $trim_req = trim($tab);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "yur_eq":
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

