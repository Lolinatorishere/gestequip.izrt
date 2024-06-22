<?php

function create_group($data_request , $pdo){
try{
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
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_create_inputs($data_request ,  $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");
    try{
        $sql = create_insertion_generator($data_request , " user_groups " , "group" , 1);
        $statement = $pdo->prepare($sql);
        foreach($data_request["group"] as $key => &$value){
            $statement->bindParam(":" . $key , $value);
        }
        $statement->execute();
        $group_id = $pdo->lastInsertId();
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] = $sql;
        throw new Exception("Server_Error_CG0001");
    }
    throw new Exception("Created_Group");
}catch(Exception $e){
    switch($e->getMessage()){
        case 'User Created':
            $loggable["type"] = "Created_Group";
            $loggable["status"] = "OK";
            $loggable["group_id"] = $group_id;
            $ret["server_message"] = "Equipment Created";
            $request = array("fetch" => " * "
                            ,"table" => " user_groups "
                            ,"counted" => 1
                            ,"specific" => " id =" . $group_id
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
