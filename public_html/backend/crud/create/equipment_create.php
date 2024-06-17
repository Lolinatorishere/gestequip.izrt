<?php
function create_equipment_create_insertion($request , $db_table , $input_type){
    $values = array();
    $columns = array();
    $total_specific_inputs = count($request[$input_type]);
    foreach($request[$input_type] as $key => $value){
        $column = "`" . $key . "`";
        if(is_bool($value)){
            if($value === true){
                $value = 1;
            }
            if($value === false){
                $value = 0;
            }
        }
        $input = "'" . $value . "'";
        array_push($columns, $column);
        array_push($values, $input);
    }
    if(count($columns) !== count($values))
        return 0;
    $create_request = array("multiple" => 1
                           ,"table" => $db_table
                           ,"columns" => $columns
                           ,"values" => $values
                           );
    return common_insert_query($create_request);
}

function create_equipment_users_groups_insertion($request , $equipment_id){
    $columns = array(" `user_id`, `group_id`, `equipment_id`, `user_permission_level`, `status`");
    $values = array();
    $eq_id = " '" . $equipment_id . "' ";
    $us_id = " '" . $request["user_id"] . "' ";
    $gp_id = " '" . $request["group_id"] . "' ";
    if(!isset($request["user_permission_level"])){
        $perm_lvl = " '0' ";
    }else{
        $perm_lvl = " '" . $request["user_permission_level"] ."' ";
    }
    $status = "0";
    array_push($values , $us_id);
    array_push($values , $gp_id);
    array_push($values , $eq_id);
    array_push($values , $perm_lvl);
    array_push($values , $status);
    $create_request = array("multiple" => 1
                           ,"table" => " users_inside_groups_equipments "
                           ,"columns" => $columns
                           ,"values" => $values
                           );
    return common_insert_query($create_request);
}

function create_equipment($request , $pdo){
try{
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "User not created";
    $error_message = array();
    $loggable = array("origin" => "Equipment_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => $request["group_id"]
                     ,"user_id" => $request["user_id"]
                     );
    $loggable["message"]["userInput"] = $request;
    // Chacks User Authentication
    if(user_group_request_authentication($request , $pdo) !== 1)
        throw new Exception("Authentication");
    // Validates inputs
    $validation_guard = validate_external_create_inputs($request , $pdo , $error_message);
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
        throw new Exception("Validation", 1);
    }
    $request["default"]["equipment_type"] = get_equipment_type($request["equipment_type"] , $pdo , "id");
    $sql = create_equipment_create_insertion($request , " equipment " , "default");
    try{
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $equipment_id = $pdo->lastInsertId();
        $loggable["message"]["default_sql"] = $sql;
        $loggable["message"]["default_inserted_id"] = $equipment_id;
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["request"] = $request;
        $loggable["message"]["sql"] =  $sql;
        throw new Exception("Server Error : C0001");
    }
    try{
        $request["specific"]["equipment_id"] = $equipment_id;
        $sql = create_equipment_create_insertion($request , " " . $request["equipment_type"] , "specific");
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $specifics_id = $pdo->lastInsertId();
        $loggable["message"]["specific_sql"] = $sql;
        $loggable["message"]["specific_inserted_id"] = array("type_id" => $specifics_id
                                                            ,"eq_type" => $request["equipment_type"]
                                                            );
    }catch(PDOException $e){
        $deletion_request = array("table" => " equipment "
                                 ,"specific" => "id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        $loggable["message"]["request"] = $request;
        $loggable["message"]["sql"] =  $sql;
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        throw new Exception("Server Error : C0002");
    }
    try{
        $update_request = array("table" => " equipment "
            ,"columns" => array("registration_lock")
            ,"values" => array("1")
            ,"specific" => "id = " . $equipment_id
        );
        $was_updated = update_equipment($update_request , $pdo);
        if(isset($was_updated["PDOException"]))
            throw new PDOException($was_updated["PDOException"]);
    }catch(PDOException $e){
        $deletion_request = array("table" => $request["equipment_type"]
                                 ,"specific" => "equipment_id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        $deletion_request = array("table" => " equipment "
                                 ,"specific" => "id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        $loggable["message"]["request"] = $request;
        $loggable["message"]["sql"] =  $sql;
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        throw new Exception("Server Error : C0003");
    }
    try{
        $sql = create_equipment_users_groups_insertion($request , $equipment_id);
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $loggable["message"]["users_inside_groups_equipments_ids"] = array($request["user_id"] , $request["group_id"] , $equipment_id);
        $loggable["message"]["users_inside_groups_equipments_sql"] = $sql;
    }catch(PDOException $e){
        $loggable["message"]["request"] = $request;
        $loggable["message"]["sql"] =  $sql;
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        throw new Exception("equipment_Group query not made");
    }
    throw new Exception("Equipment Created");
}catch(Exception $e){
    switch($e->getMessage()){
        case 'Equipment Created':
            $loggable["type"] = "Created_Equipment";
            $loggable["status"] = "OK";
            $loggable["equipment_id"] = $equipment_id;
            $ret["server_message"] = "Equipment Created";
            $ret["message"] = get_equipment(" * " , $equipment_id , $pdo);
            break;
        case 'Authentication':
            $loggable["type"] = "Auth_Error";
            $loggable["status"] = "Error";
            $loggable["exception"]["authentication"] = "Unauthorised Request";
            $ret["server_message"] = "Unauthorised Access";
            $ret["message"] = array("User Credentials Invalid for Action");
            break;
        case 'Validation':
            //loggable set by validation_guard
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
    create_log($loggable , "equipment_logs" , $pdo);
    return $ret;
}
}

?>
