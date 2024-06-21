<?php

include_once query_generator_dir;

function get_pre_updated_user_information($data_request , $pdo){
    $previous_info = array();
    if(isset($data_request["default"])){
        $request = array("fetch" => " * "
                         ,"table" => " users "
                         ,"counted" => 1
                         ,"specific" => "id=\"" . $data_request["user_id"] . "\""
                     );
        $previous_info = get_query($request , $pdo)["items"];
    }
    $request = array("fetch" => " * "
                  ,"table" => " sudo_group "
                  ,"counted" => 1
                  ,"specific" => " user_id=" . $data_request["user_id"]
                  );
    $previous_info["sudo_user"] = get_query($request , $pdo)["items"];
    return $previous_info;
}
// todo everything kek
// i swear to god im jumping off a cliff due to stupidity
function update_user($data_request , $pdo){
    $loggable = array("origin" => "User_Update"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"user_id" => $data_request["user_id"]
                     );
try{
    $loggable["message"]["userInput"] = $data_request;
    $internal_message = array();
    $error_message = array();
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_update_inputs($data_request , $pdo , $error_message);
    if($validation_guard !== 1){
        $loggable["type"] = "Input_Error";
        $loggable["status"] = "Warning";
        if($validation_guard !== 0){
            $loggable["status"] = "Error";
            $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
        }
        throw new Exception("Validation");
    }
    $loggable["message"]["previousInfo"] = get_pre_altered_user_information($data_request , $pdo); 
    die(); //todo update functionality lamayo;
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
            $ret["message"] = $error_message;
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

?>
