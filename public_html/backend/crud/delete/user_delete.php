<?php

include_once query_generator_dir;

//todo if there is a PDOException error during the deletion of the equipment there should 
//be a funciton that reinstates the previous information into the server in the correct places
function unaltered_user_information_full($data_request , $pdo){
    $request = array("fetch" => " * "
                   ,"table" => " equipment "
                   ,"counted" => 1
                   ,"specific" => "id=" . $data_request["equipment_id"]
                   );
    $previous_info["default"] = get_queries($request , $pdo)["items"];
    $equipment_type = get_equipment_type($previous_info["default"][0]["equipment_type"] , $pdo , "name");
    $request = array("fetch" => " * "
                   ,"table" => $equipment_type
                   ,"counted" => 1
                   ,"specific" => "equipment_id=" . $data_request["equipment_id"]
                   );
    $previous_info["specific"] = get_queries($request , $pdo)["items"];
    $request = array("fetch" => " * "
                   ,"table" => " users_inside_groups_equipments "
                   ,"counted" => 1
                   ,"specific" => "equipment_id=" . $data_request["equipment_id"]
                   );
    $previous_info["user_references"] = get_queries($request , $pdo)["items"];
    return $previous_info;
}

function unaltered_equipment_information_reference($data_request , $pdo){
    $request = array("fetch" => " * "
                   ,"table" => " users_inside_groups_equipments "
                   ,"counted" => 1
                   ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                . " AND group_id=" . $data_request["group_id"]
                                . " AND user_id=" . $data_request["user_id"]
                   );
   $previous_info["user_references"] = get_queries($request , $pdo)["items"];
   return $previous_info;
}

function delete_user($data_request , $pdo){
try{
    $loggable = array("origin" => "Equipment_Delete"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"user_id" => $data_request["user_id"]
                     );
    $loggable["message"]["userInput"] = $data_request;
    $error_message = array();
    $deletion_guard = 0;
    $ret = array("server_message" => ""
                ,"message" => array()
                );
    if(user_group_request_authentication($data_request , $pdo) !== 1){
        if(equipment_authentication($data_request , $pdo) !== 1)
            throw new Exception("Authentication");
    }
    $validation_guard = validate_external_delete_inputs($data_request , $pdo , $error_message);
    if($validation_guard !== 1){
        switch($validation_guard){
        case 0:
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Warning";
            throw new Exception("Validation");
            break;
        //Multiple Equipment detected return to frontend 
        //a question if they want to remove the association to the user
        // or the equipment (thus removing all references to it from the db)
        case -1:
            if(isset($data_request["response"])){
                switch($data_request["response"]){
                case 'user':
                    $deletion_guard = 1;
                    break;
                case 'equipment':
                    $deletion_guard = 0;
                    break;
                default:
                    throw new Exception("Question");
                }
            }else{
                throw new Exception("Question");
            }
            break;
        case -2:
            //no equipment in the db with the id
            $loggable["type"] = "Input_Error";
            $loggable["status"] = "Warning";
            throw new Exception("Validation");
        default:
            $loggable["status"] = "Error";
            $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
            throw new Exception("Validation");
        }
    }
    switch($deletion_guard){
        case 0:
        try{
            $loggable["message"]["previousInfo"] = unaltered_equipment_information_full($data_request , $pdo);
            $equipment_type_id = get_equipment(" equipment_type " , $data_request["equipment_id"] , $pdo);
            $equipment_type = get_equipment_type($equipment_type_id["items"][0]["equipment_type"] , $pdo , "name");
            $delete_specific_request = array("table" => $equipment_type
                                            ,"specific" => "equipment_id=" . $data_request["equipment_id"]
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
            $delete_default_request = array("table" => " equipment "
                                           ,"specific" => "id=" . $data_request["equipment_id"]
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
            $delete_user_reference_request = array("table" => " users_inside_groups_equipments "
                                                  ,"specific" => "equipment_id=" . $data_request["equipment_id"]
                                                  );
            $delete = delete_query($delete_user_reference_request , $pdo);
            if(isset($delete["PDOException"]))
                throw new PDOException($update["PDOException"]);
            $loggable["message"]["reference"] = $delete_user_reference_request;
            throw new Exception("Deleted");
        }catch(PDOException $e){
            $loggable["exception"]["reference"] = $e->getMessage();
            throw new Exception("Server_Error");
        }
        case 1:
        try{
            $loggable["message"]["previousInfo"] = unaltered_equipment_information_reference($data_request , $pdo);
            $delete_user_reference_request = array("table" => " users_inside_groups_equipments "
                                                  ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                                               . " AND group_id=" . $data_request["group_id"]
                                                               . " AND user_id=" . $data_request["user_id"]
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
    }
}catch(Exception $e){
    switch($e->getMessage()){
        case "Question":
            $ret["server_message"] = "Multiple Users/Groups Assigned To Equipment";
            $ret["message"]["title"] = "There are multiple people or groups assigned to the equipment being deleted";
            $ret["message"]["content"] = "Please Choose between removing the equipment and all the references or just the specific reference";
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
    create_log($loggable , "equipment_logs" , $pdo);
    return $ret;
}
}


?>
