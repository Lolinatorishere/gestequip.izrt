<?php

function get_pre_updated_equipment_information($data_request , $pdo){
    $previous_info = array();
    if(isset($data_request["default"])){
        printLog("lasdnfasdjhf");
        $request = array("fetch" => " * "
                         ,"table" => " equipment "
                         ,"counted" => 1
                         ,"specific" => "id=\"" . $data_request["equipment_id"] . "\""
                     );
        $previous_info["default"] = get_query($request , $pdo)["items"];
    }
    if(isset($data_request["default"])){
        printLog("lasdnfasdjhf");
        $request = array("fetch" => " * "
            ,"table" => $data_request["equipment_type"]
            ,"counted" => 1
            ,"specific" => "equipment_id=\"" . $data_request["equipment_id"] . "\""
        );
        $previous_info["specific"] = get_query($request , $pdo)["items"];
    }
    if(isset($data_request["user_permission_level"])){
        printLog("lasdnfasdjhf");
        $request = array("fetch" => " * "
                      ,"table" => " users_inside_groups_equipments "
                      ,"counted" => 1
                      ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                     . " group_id=" . $data_request["group_id"]
                                     . " user_id=" . $data_request["user_id"]
                      );
        $previous_info["user_group_equipment"] = get_query($request , $pdo)["items"];
    }
    return $previous_info;
}

// i swear to god im jumping off a cliff due to stupidity
function external_update_equipment($data_request , $pdo){
    $loggable = array("origin" => "Equipment_Update"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"user_id" => $_SESSION["id"]
                     ,"equipment_id" => $data_request["equipment_id"]
                     ,"group_id" => $data_request["group_id"]
                     );
try{
    $loggable["message"]["userInput"] = $data_request;
    $internal_message = array();
    $error_message = array();
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if(user_group_request_authentication($data_request , $pdo) !== 1){
        if(equipment_authentication($data_request , $pdo) !== 1){
            throw new Exception("Authentication");
        }
    }
    $validation_guard = validate_external_update_inputs($data_request , $pdo , $error_message);
    if($validation_guard !== 1){
        $loggable["type"] = "Input_Error";
        $loggable["status"] = "Warning";
        switch($validation_guard){
            case -1:
                break;
            case -2:
                break;
            case -3:
                break;
            case -4:
                break;
            case -5:
                break;
            default:
                $loggable["status"] = "Error";
                $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
                break;
        }
        throw new Exception("Validation");
    }
    $loggable["message"]["previousInfo"] = get_pre_updated_equipment_information($data_request , $pdo); 
    if(isset($data_request["default"])){
    try{
        $columns = array();
        $values = array();
        foreach($data_request["default"] as $key => $value){
            array_push($columns , $key);
            array_push($values , $value);
        }
        $request = array("table" => " equipment "
            ,"columns" => $columns
            ,"values" => $values
            ,"specific" => "id =" . $data_request["equipment_id"]
        );
        $update = update_equipment($request , $pdo);
        if(isset($update["PDOException"]))
            throw new PDOException($update["PDOException"]);
        foreach($data_request["default"] as $key => $value){
            array_push($internal_message , "input " , $value , " updated");
        }
        $loggable["message"]["default"] = $request;
    }catch(PDOException $e){
        $loggable["exception"]["default"] = $e->getMessage();
        throw new Exception("Default");
    }
    }
    if(isset($data_request["specific"])){
    try{
        $columns = array();
        $values = array();
        foreach($data_request["specific"] as $key => $value){
            array_push($columns , $key);
            array_push($values , $value);
        }
        $request = array("table" => $data_request["equipment_type"]
                        ,"columns" => $columns
                        ,"values" => $values
                        ,"specific" => "equipment_id =" . $data_request["equipment_id"]
                        );
        $update = update_equipment($request , $pdo);
        if(isset($update["PDOException"]))
            throw new PDOException($update["PDOException"]);
        if(!isset($update["success"])){
            $loggable["exception"]["specific"] = $e->getMessage();
            throw new Exception("Specific");
        }
        foreach($data_request["specific"] as $key => $value){
            array_push($internal_message , "input " , $value , " updated");
        }
        $loggable["message"]["specific"] = $request;
    }catch(PDOException $e){
        $loggable["exception"]["specific"] = $e->getMessage();
        throw new Exception("Specific");
    }
    }
    if(isset($data_request["user_permission_level"])){
    try{
        $request = array("table" => " users_inside_groups_equipments "
                        ,"columns" => $columns = array(" user_permission_level ")
                        ,"values" => $values = array($data_request["user_permission_level"])
                        ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                     . " group_id=" . $data_request["group_id"]
                                     . " user_id=" . $data_request["user_id"]
                        );
        $update = update_equipment($request , $pdo);
        array_push($internal_message , "user permission level updated");
        $loggable["message"]["user_permission_level"] = $request;
    }catch(PDOException $e){
        $loggable["exception"]["user_permission_level"] = $e->getMessage();
        throw new Exception("Equipment_Permission");
    }
    }
    $ret["message"]["title"] = "Equipment Successfully updated";
    throw new Exception("Updated");
}catch(Exception $e){
    $ret["server_message"] = "";
    switch($e->getMessage()){
        case "Updated":
            $loggable["type"] = "Updated_Equipment";
            $loggable["status"] = "OK";
            $ret["server_message"] = "Updated Equipment";
            break;
        case "Authentication":
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "User not Authorized";
            $ret["message"]["error"] = "User Credentials Invalid for Action";
            break;
        case "Validation":
            //set in validation_guard
            $loggable["message"]["user_input_error"] = $error_message;
            $ret["server_message"] = "Invalid User Input";
            break;
        default:
            $loggable["type"] = "Server_Error";
            $loggable["status"] = "Error";
            $ret["message"]["title"] =  "Issue Updating The Equipment";
            if(count($internal_message) > 0){
                $ret["message"]["updated"]["title"] = "Only the following inputs have been";
                $ret["message"]["updated"]["inputs"] = $internal_message;
            }
            break;
    }
    create_log($loggable , "equipment_logs" , $pdo);
    return $ret;
}
}

function update_equipment($request , $pdo){
try{
    $sql_error = array("error" => "error");
    $ret = array();
    if($request === "error")
        return $sql_error;
    $sql = common_update_query($request);
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $ret["success"] = "success";
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e,true));
    $sql_error["PDOException"] = $e;
    return $sql_error;
}
}
?>
