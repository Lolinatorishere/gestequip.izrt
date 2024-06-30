<?php
include_once query_generator_dir;

function get_pre_updated_group_information($data_request , $pdo){
    $previous_info = array();
    if(isset($data_request["user"])){
        $request = array("fetch" => " * "
                         ,"table" => " user_groups "
                         ,"counted" => 1
                         ,"specific" => "id=\"" . $data_request["group_id"] . "\""
                     );
        $previous_info["groups"] = get_queries($request , $pdo)["items"];
    }
    $sudo_user = get_queries($request , $pdo);
    return $previous_info;
}
//
// i swear to god im jumping off a cliff due to stupidity
function update_group($data_request , $pdo){
    $loggable = array("origin" => "Group_Update"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $data_request["group_id"]
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
    $loggable["message"]["previousInfo"] = get_pre_updated__information($data_request , $pdo); 
    try{
        foreach($data_request["group"] as $key => $value){
            array_push($columns , $key);
            array_push($values , $value);
        }
        $columns = array();
        $values = array();
        $request = array("table" => " user_groups "
                        ,"columns" => $columns
                        ,"values" => $values
                        ,"specific" => "id =" . $data_request["group_id"]
                        );
        $update = update_query($request , $pdo);
        if(isset($update["PDOException"]))
            throw new PDOException($update["PDOException"]);
    }catch(PDOException $e){
        $loggable["exception"]["group"] = $e->getMessage();
        $loggable["message"]["group"] =  $data_request["group"];
        $error = explode(':' , $e->getMessage());
        if(trim($error[1]) === "SQLSTATE[23000]"){
            $error_messages;
            preg_match_all("/'([^']+)'/" , $error[3] , $error_messages);
            $error_messages[0][1] = explode('.' , $error_messages[0][1])[1];
            $error_messages[0][1] = explode('\'' , $error_messages[0][1])[0];
            $exception = $error_messages[0][1] . " is not unique, inserted " . $error_messages[0][0];
            throw new Exception($exception);
        }
        throw new Exception("Server_Error_UE0001");
    }
    throw new Exception("Updated");
}catch(Exception $e){
    $ret["server_message"] = "";
    switch($e->getMessage()){
        case "Updated":
            $loggable["type"] = "Updated_group";
            $loggable["status"] = "OK";
            $ret["server_message"] = "Updated_group";
            break;
        case "Authentication":
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "User not Authorized";
            $ret["message"]["error"] = "User Credentials Invalid for Action";
            break;
        case "Validation":
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Error";
            $loggable["message"]["user_input_error"] = $error_message;
            $ret["server_message"] = "Invalid User Inputs";
            $ret["message"] = $error_message;
            break;
        default:
            $loggable["type"] = "Server_Error";
            $loggable["status"] = "Error";
            $ret["message"]["title"] =  "Issue Updating The Group";
            $loggable["exception"]["thrown_exception"] = $e->getMessage();
            $ret["server_message"] = "Opperation could not Be Completed";
            $ret["message"] = $e->getMessage();
            break;
    }
    create_log($loggable , "group_logs" , $pdo);
    return $ret;
}
}

?>
