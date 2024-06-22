<?php


function create_user_group_reference($data_request , $pdo , $loggable){
    
}

function create_user_group_equipment_reference($data_request , $pdo , $loggable){
    
}

function create_reference($data_request , $pdo){
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "Group Not Created";
    $error_message = array();
    $loggable = array("origin" => "Group_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => ""
                     );
    $loggable["message"]["userInput"] = $data_request["group"];

        throw new Exception("Authentication");
    $validation_guard = validate_external_create_inputs($data_request ,  $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");
    switch($data_request["reference"]){
        case "user_group":
            return create_user_group_reference($data_request , $pdo);
        case "user_group_equipment":
            return create_user_group_equipment_reference($data_request , $pdo);
    }
}

?>
