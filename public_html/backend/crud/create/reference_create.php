<?php


function create_group_user_reference($data_request , $pdo){
try{
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "Reference Not Created";
    $error_message = array();
    $loggable = array("origin" => "Reference_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => ""
                     ,"user_id" => ""
                     ,"destination" => "reference_logs"
                     );
    $success = "Created_Reference";
    if($_SESSION["user_type"] !== "Admin")
        if(user_group_request_authentication($data_request , $pdo) !== 1)
            throw new Exception("Authentication");
    $validation_guard = validate_external_inputs($data_request , "create_reference" , "users_inside_groups" , $pdo , $error_message);
    if(validate_reference_existence($data_request , $pdo) === 1){
        $error_message = "Reference Already Exists";
        $validation_guard = 0;
    }
    if($validation_guard !== 1)
        throw new Exception("Validation");
    $loggable["message"]["userInput"] = $data_request["create_reference"];
    try{
        if(!isset($data_request["create_reference"]["user_permission_level"])){
            $data_request["create_reference"]["user_permission_level"] = 0;
        }
        $data_request["create_reference"]["group_id"] = $data_request["group_id"];
        $data_request["create_reference"]["user_id"] = $data_request["user_id"];
        $sql = create_insertion_generator($data_request , " users_inside_groups " , "create_reference" , 1);
        $statement = $pdo->prepare($sql);
        foreach($data_request["create_reference"] as $key => &$value){
            $statement->bindParam(":" . $key , $value);
        }
        $statement->execute();
        $pdo_previd = $pdo->lastInsertId();
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] = $sql;
        $loggable["status"] = "Error";
        throw new Exception("Server_Error_CGR0001");
    }
    $loggable["type"] = "Reference_Created";
    $ret["message"] = "Successfully linked user:" . $data_request["user_id"] ." to group:" . $data_request["group_id"];
    throw new Exception("Created_Reference");
}catch(Exception $e){
    $loggable["group_id"] = $data_request["group_id"];
    $loggable["user_id"] = $data_request["user_id"];
    log_create($ret , $success , $e , $loggable , $error_message , $pdo , $pdo_previd);
    return $ret;
}
}

function create_reference($data_request , $pdo){
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $error_message;
try{
    if($_SESSION["user_type"] !== "Admin")
        if(user_group_request_authentication($data_request , $pdo) !== 1)
            throw new Exception("Authentication");
    if(!isset($data_request["reference"])){
        $error_message = "Reference Unset";
        throw new Exception("Validation");
    }
    if(!isset($data_request["create_reference"])){
        $error_message = "Reference Creation Unset";
        throw new Exception("Validation");
    }
    switch($data_request["reference"]){
        case "user_group":
            $validation_guard = validate_external_inputs($data_request , "create_reference" , "users_inside_groups" , $pdo , $error_message);
            if(!isset($data_request["user_id"]) ||
               !isset($data_request["group_id"])){
                $error_message = "Id's not Set";
                $validation_guard = 0;
            }
            if($data_request["group_id"] <= 0 ||
               $data_request["user_id"] <= 0){
                $error_message = "Invalid Id's Used";
                $validation_guard = 0;
            }
            if($validation_guard !== 1){
                throw new Exception("Validation");
            }
            return create_group_user_reference($data_request , $pdo);
        case "user_group_equipment":
            $validation_guard = validate_external_inputs($data_request , "reference" , "users_inside_groups_equipments" , $pdo , $error_message);
            if(!isset($data_request["equipment_id"]) ||
               !isset($data_request["user_id"]) ||
               !isset($data_request["group_id"])){
                $error_message = "Id's not Set";
                $validation_guard = 0;
            }
            if($data_request["equipment_id"] <= 0 ||
               $data_request["group_id"] <= 0 ||
               $data_request["user_id"] <= 0){
                $error_message = "Invalid Id's Used";
                $validation_guard = 0;
            }
            if($validation_guard !== 1){
                throw new Exception("Validation");
            }
            return create_user_group_equipment_reference($data_request , $pdo);
        default:
            return "Reference Invalid";
    }
}catch(Exception $e){
    switch ($e->getMessage()) {
        case 'Validation':
            $ret["server_message"] = "Invalid Inputs";
            $ret["message"] = $error_message;
            break;
        case 'Authentication':
            $ret["server_message"] = "Invalid User Authentication";
            $ret["message"] = "User Not Authorised";
        default:
            $ret["server_message"] = "Server Error";
            $ret["message"] = "Error Occured With the Server";
            break;
    }
    return $ret;
}
}

?>
