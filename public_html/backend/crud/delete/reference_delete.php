<?php


function delete_user_group_reference($data_request , $pdo){
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
                     ,"group_id" => $data_request["group_id"]
                     ,"user_id" => $data_request["user_id"]
                     ,"destination" => "reference_logs"
                     );
    $success = "Created_Reference";
    if($_SESSION["user_type"] !== "Admin")
        if(user_group_request_authentication($data_request , $pdo) !== 1)
            throw new Exception("Authentication");
    if(validate_user_in_db($data_request["user_id"] , $pdo) !== 1 ){
        $error_message = "User Does Not Exist";
        throw new Exception("Validation");
    }
    if(validate_group_in_db($data_request["group_id"] , $pdo) !== 1){
        $error_message = "Group Does Not Exist";
        throw new Exception("Validation");
    }
    if(validate_reference_existence($data_request , $pdo) !== 1){
        $error_message = "Reference Doesnt Exist";
        throw new Exception("Validation");
    }
    $loggable["message"]["userInput"] = $data_request;
    try{
        $request = array("table" => " users_inside_groups "
                        ,"specific" => " user_id=" . $data_request["user_id"]
                                     . " AND group_id=" . $data_request["group_id"]
                        );
        $delete = delete_query($request , $pdo);
        if(isset($delete["PDOException"]))
            throw new PDOException($delete["PDOException"]);
    }catch(PDOException $e){
        $loggable["group_id"] = $data_request["group_id"];
        $loggable["user_id"] = $data_request["user_id"];
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $ret["message"] = "Reference not Deleted";
        $loggable["status"] = "Error";
        throw new Exception("Server_Error_DUGR0001");
    }
        $loggable["type"] = "Reference_Deleted";
        $ret["message"] = "Successfully Deleted Referenece group:" . $data_request["group_id"] . " and user:" . $data_request["user_id"];
        throw new Exception("Deleted");
}catch(Exception $e){
    $loggable["group_id"] = $data_request["group_id"];
    $loggable["user_id"] = $data_request["user_id"];
    log_create($ret , $success , $e , $loggable , $error_message , $pdo , $pdo_previd);
    return $ret;
}
}

function delete_user_group_equipment_reference($data_request , $pdo){
try{
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "Reference Not Created";
    $error_message = array();
    $loggable = array("origin" => "Delete_Reference"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $data_request["group_id"]
                     ,"user_id" => $data_request["user_id"]
                     ,"equipment_id" => $data_request["equipment_id"]
                     ,"destination" => "reference_logs"
                     );
    $success = "Deleted";
    if($_SESSION["user_type"] !== "Admin")
        if(user_group_request_authentication($data_request , $pdo) !== 1)
            throw new Exception("Authentication");
    if(validate_user_in_db($data_request["user_id"] , $pdo) !== 1){
        $error_message = "User Does Not Exist";
        throw new Exception("Validation");
    }
    if(validate_group_in_db($data_request["group_id"] , $pdo) !== 1){
        $error_message = "Group Does Not Exist";
        throw new Exception("Validation");
    }
    if(validate_equipment_in_db($data_request["equipment_id"] , $pdo) !== 1){
        $error_message = "Equipment Does Not Exist";
        throw new Exception("Validation");
    }
    if(validate_reference_existence($data_request , $pdo) !== 1){
        $error_message = "Reference Doesnt Exist";
        throw new Exception("Validation");
    }
    $loggable["message"]["userInput"] = $data_request;
    try{
        $request = array("table" => " users_inside_groups_equipments "
                        ,"specific" => "equipment_id=" . $data_request["equipment_id"]
                                     . " AND user_id=" . $data_request["user_id"]
                                     . " AND group_id=" . $data_request["group_id"]
                        );
        $delete = delete_query($request , $pdo);
        if(isset($delete["PDOException"]))
            throw new PDOException($update["PDOException"]);
    }catch(PDOException $e){
        $loggable["group_id"] = $data_request["group_id"];
        $loggable["user_id"] = $data_request["user_id"];
        $loggable["equipment_id"] = $data_request["equipment_id"];
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] = $sql;
        $loggable["status"] = "Error";
        throw new Exception("Server_Error_DUGER0001");
    }
        $loggable["type"] = "Reference_Deleted";
        $ret["message"] = "Successfully Deleted Referenece equipment:" . $data_request["equipment_id"] ." to group:" . $data_request["group_id"] . " and user:" . $data_request["user_id"];
        throw new Exception("Deleted");
}catch(Exception $e){
    $loggable["group_id"] = $data_request["group_id"];
    $loggable["user_id"] = $data_request["user_id"];
    log_create($ret , $success , $e , $loggable , $error_message , $pdo , $pdo_previd);
    return $ret;
}
}

function delete_reference($data_request , $pdo){
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
    switch($data_request["reference"]){
        case "user_group":
            $validation_guard = 1;
            if(!isset($data_request["user_id"]) ||
               !isset($data_request["group_id"])){
                $error_message = "Id's not Set";
                $validation_guard = 0;
            }
            if($data_request["group_id"] <= 1 ||
               $data_request["user_id"] <= 1){
                $error_message = "Invalid Id's Used";
                $validation_guard = 0;
            }
            if($validation_guard !== 1){
                throw new Exception("Validation");
            }
            return delete_user_group_reference($data_request , $pdo);
        case "user_group_equipment":
            $validation_guard = 1;
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
            return delete_user_group_equipment_reference($data_request , $pdo);
        default:
            return "Invalid Reference";
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
