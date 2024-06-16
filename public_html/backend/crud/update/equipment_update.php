<?php

// i swear to god im jumping off a cliff due to stupidity
function external_update_equipment($data_request , $pdo){
    $loggable = array("type" => " Update Equipment"
                     ,"status" => " ";
                     ,"exception" => array()
                     ,"message" => array()
                     ,"user_id" => $_SESSION["id"]
                     ,"equipment_id" => $data_request["equipment_id"]
                     ,"group_id" => $data_request["group_id"]
                     );
try{
    $internal_message = array();
    $error_message = array();
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if(user_group_request_authentication($data_request , $pdo) !== 1){
        if(equipment_authentication($data_request , $pdo) !== 1){
            $loggable["exception"]["auth"] = "Unauthorised Request";
            $loggable["status"] = "Warning";
            throw new Exception("Authentication", 1);
        }
    }
    $validation_guard = validate_external_update_inputs($data_request , $pdo , $error_message);
    if($validation !== 1){
        $loggable["status"] = "Warning";
        switch($validation_guard){
            case -1:
                $loggable["message"]["user_input_error"] = "No Equipment Was Selected";
                break;
            case -2:
                $loggable["message"]["user_input_error"] = $error_message;
                break;
            case -3:
                $loggable["message"]["user_input_error"] = $error_message;
                break;
            case -4:
                $loggable["message"]["user_input_error"] = $error_message;
                break;
            case -5:
                $loggable["message"]["user_input_error"] = $error_message;
                break;
            default
                $loggable["type"] = "Server Error";
                $loggable["type"] = "Invalid Validation Check, check validation code for possible bugs";
            break;
        }
        throw new Exception("Validation", 1);
    }
    if(isset($data_request["default"])){
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
        if(!isset($update["success"])){
            $loggable["exception"]["default"] = $update["PDOException"]["message:protected"];
            throw new Exception("Error Processing Request", 1);
        }
        foreach($data_request["default"] as $key => $value){
            array_push($internal_message , "input " , $value , " updated");
        }
        array_push($loggable["message"]["default"] ,  $request);
    }
    if(isset($data_request["specific"])){
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
        if(!isset($update["success"])){
            $loggable["exception"]["specific"] = $update["PDOException"]["message:protected"];
            throw new Exception("Error Processing Request", 1);
        }
        foreach($data_request["specific"] as $key => $value){
            array_push($internal_message , "input " , $value , " updated");
        }
        $loggable["message"]["specific"] = $request;
    }
    if(isset($data_request["user_permission_level"])){
        $request = array("table" => " users_inside_groups_equipments "
                        ,"columns" => $columns = array(" user_permission_level ")
                        ,"values" => $values = array($data_request["user_permission_level"])
                        ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                     . " group_id=" . $data_request["group_id"]
                                     . " user_id=" . $data_request["user_id"]
                        );
        $update = update_equipment($request , $pdo);
        if(!isset($update["success"])){
            $loggable["exception"]["user_permission_level"] = $update["PDOException"]["message:protected"];
            throw new Exception("Error Processing Request", 1);
        }
        array_push($internal_message , "user permission level updated");
        $loggable["message"]["user_permission_level"] = $request;
    }
    array_push($ret["message"] , "Equipment Successfully updated");
    throw new Exception("Equipment updated", 1);
}catch(Exception $e){
    if($e === "Equipment updated"){
        $ret["server_message"] = "Success";

        return $ret;
    }
    $ret["server_message"] = "";
    if($e === "Authentication"){
        $ret["server_message"] = "User not Authorized";
        $ret["message"] = array("");
    }
    if($e === "Validation"){
        $ret["server_message"] = $error_message;
        $ret["message"] = array("");
    }
    array_push($ret["message"] , "Issue Updating The Equipment");
    if(count($internal_message) > 0){
        array_push($ret["message"] , "Only the following inputs have been inserted");
        array_push($ret["message"] , $internal_message);
    }
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
