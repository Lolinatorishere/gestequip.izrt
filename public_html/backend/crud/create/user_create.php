<?php

include_once query_generator_dir;

function create_user($data_request , $pdo){
try{
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "User not created";
    $error_message = array();
    $loggable = array("origin" => "User_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $data_request["group_id"]
                     );
    $loggable["message"]["userInput"] = $data_request;
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_create_inputs($data_request ,  $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");
    try{
        //todo make it so the insertion generator has a PDO function kek
        $sql = create_insertion_generator($data_request , " users " , "user" );
        printLog($sql);
    }catch(PDOException $e){
        
    }
}catch(Exception $e){
    switch($e->getMessage()){
        case 'User Created':
            $loggable["type"] = "Created_User";
            $loggable["status"] = "OK";
            $loggable["equipment_id"] = $equipment_id;
            $ret["server_message"] = "Equipment Created";
            $user_return = array(" id , username , users_name , email , phone_number , regional_indicator , date_created , account_status");
            $request = array("fetch" => $user_return
                            ,"table" => " users "
                            ,"counted" => 1
                            ,"specific" => " id =" . $created_user_id
                            );
            $ret["message"] = get_query($request , $pdo);
            break;
        case 'Authentication':
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "Unauthorised Access";
            $ret["message"] = array("User Credentials Invalid for Action");
            break;
        case 'Validation':
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Error";
            $loggable["message"]["user_input_error"] = $error_message;
            $ret["server_message"] = "Invalid User Inputs";
            $ret["message"] = $error_message;
            break;
        default:
            $loggable["type"] = "Server_Error";
            $loggable["log_status"] = "Error";
            if(isset($equipment_id)){
                $loggable["equipment_id"] = $equipment_id;
                $loggable["exception"]["incomplete_creation"] = "The following equipment had an error inserting information " . $equipment_id;
            }
            $loggable["message"]["user_inputs"] = $request;
            $ret["server_message"] = "Opperation could not Be Completed";
            $ret["message"] = $e->getMessage();
            break;
    }
    create_log($loggable , "user_logs" , $pdo);
    return $ret;
}
}

?>
