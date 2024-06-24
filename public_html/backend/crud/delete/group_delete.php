<?php

include_once query_generator_dir;

//todo if there is a PDOException error during the deletion of the equipment there should 
//be a funciton that reinstates the previous information into the server in the correct places
function unaltered_group_information_full($data_request , $pdo){
    $request = array("fetch" => " * "
                   ,"table" => " user_groups "
                   ,"counted" => 1
                   ,"specific" => "id=" . $data_request["group_id"]
                   );
    $previous_info["group"] = get_queries($request , $pdo)["items"];
    $request = array("fetch" => " * "
                   ,"table" => " users_inside_groups "
                   ,"counted" => 1
                   ,"specific" => "group_id=" . $data_request["group_id"]
                   );
    $previous_info["group_user_references"] = get_queries($request , $pdo)["items"];
    $request = array("fetch" => " * "
                   ,"table" => " users_inside_groups_equipments "
                   ,"counted" => 1
                   ,"specific" => "group_id=" . $data_request["group_id"]
                   );
    $previous_info["group_user_references"] = get_queries($request , $pdo)["items"];
    return $previous_info;
}

function unaltered_user_group_information($data_request , $pdo){
    $request = array("fetch" => " * "
                   ,"table" => " user_groups "
                   ,"counted" => 1
                   ,"specific" => "id=" . $data_request["group_id"]
                   );
    $previous_info["group"] = get_queries($request , $pdo)["items"];
    return $previous_info;
}

function delete_group($data_request , $pdo){
try{
    $loggable = array("origin" => "Group_Delete"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $data_request["group_id"]
                     );
    $loggable["message"]["userInput"] = $data_request;
    $error_message = array();
    $deletion_guard = 10;
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_delete_inputs($data_request , $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");
    $ids["group_id"] = $data_request["group_id"];
    $reference_guard = validate_group_users_references_in_db($ids , $pdo);
    if($reference_guard >= 1){
        if(isset($data_request["response"])){
            switch($data_request["response"]){
            case 'yes':
                $deletion_guard = 0;
                break;
            case 'no':
                $ret["server_message"] = "Canceled Operation";
                $ret["message"] = "the operation has been canceled";
                return $ret;
            default:
                throw new Exception("Question");
            }
        }else{
            throw new Exception("Question");
        }
    }else if($reference_guard === 0){
        $deletion_guard = 1;
    }else{
        $loggable["status"] = "Error";
        $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
        $error_message = "Invalid Validation Check ";
        throw new Exception("Validation");
    }
    switch($deletion_guard){
        case 0:
        try{
            $loggable["message"]["previousInfo"] = unaltered_group_information_full($data_request , $pdo);
            $delete_specific_request = array("table" => " users_inside_groups_equipments "
                                            ,"specific" => "group_id=" . $data_request["group_id"]
                                            );
            $delete = delete_query($delete_specific_request, $pdo);
            if(isset($delete["PDOException"]))
                throw new PDOException($update["PDOException"]);
            $loggable["message"]["deletion_request"] = $delete_specific_request;
        }catch(PDOException $e){
            $loggable["exception"]["specific"] = $e->getMessage();
            throw new Exception("Server_Error");
        }
        try{
            $delete_default_request = array("table" => " users_inside_groups "
                                           ,"specific" => "group_id=" . $data_request["group_id"]
                                           );
            $delete = delete_query($delete_default_request, $pdo);
            if(isset($delete["PDOException"]))
                throw new PDOException($update["PDOException"]);
            $loggable["message"]["default"] = $delete_default_request;
        }catch(PDOException $e){
            $loggable["exception"]["default"] = $e->getMessage();
            throw new Exception("Server_Error");
        }
        try{
            $delete_user_reference_request = array("table" => " user_groups "
                                                  ,"specific" => " id=" . $data_request["group_id"]
                                                  );
            $delete = delete_query($delete_user_reference_request , $pdo); if(isset($delete["PDOException"]))
                throw new PDOException($update["PDOException"]);
            $loggable["message"]["reference"] = $delete_user_reference_request;
            throw new Exception("Deleted");
        }catch(PDOException $e){
            $loggable["exception"]["reference"] = $e->getMessage();
            throw new Exception("Server_Error");
        }
        case 1:
        try{
            $loggable["message"]["previousInfo"] = unaltered_user_group_information($data_request , $pdo);
            $delete_user_reference_request = array("table" => " user_groups "
                                                  ,"specific" => " id=" . $data_request["group_id"]
                                                  );
            $delete = delete_query($delete_user_reference_request , $pdo);
            if(isset($delete["PDOException"]))
                throw new PDOException($update["PDOException"]);
            $loggable["message"]["deletion_request"] = $delete_user_reference_request;
            throw new Exception("Deleted");
            break;
        }catch(PDOException $e){
            $loggable["exception"]["specific"] = $e->getMessage();
            throw new Exception("Server_Error");
        }
        default:
            return["Server Validation Error"];
    }
}catch(Exception $e){
    switch($e->getMessage()){
        case "Question":
            $ret["server_message"] = "Multiple References Assigned To Object";
            $ret["message"]["title"] = "Please Confirm The Deletion of the Group";
            $ret["message"]["content"] = "There are multiple people or groups assigned to the group being deleted";
            return $ret;
        case "Deleted":
            $loggable["type"] = "Deleted_Equipment";
            $loggable["status"] = "OK";
            $ret["server_message"] = "Deleted Requested Input";
            $ret["message"]["title"] = "Success";
            $ret["message"]["content"] = "Equipment Deleted";
            break;
        case "Authentication":
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "User not Authorized";
            $ret["message"] = "User Credentials Invalid for Action";
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
            $ret["message"]["title"] =  "Issue Deleting The Equipment";
            break;
    }
    create_log($loggable , "group_logs" , $pdo);
    return $ret;
}
}

?>
