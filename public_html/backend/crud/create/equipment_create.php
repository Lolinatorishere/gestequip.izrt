<?php

include_once query_generator_dir;

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
                     ,"destination" => "equipment_logs"
                     );
    $success = "Equipment_Created";
    // Chacks User Authentication
    if($_SESSION["user_type"]!== "Admin"){
        if(user_group_request_authentication($request , $pdo) !== 1)
            throw new Exception("Authentication");
    }
    // Validates inputs
    $validation_guard = validate_external_create_inputs($request , $pdo , $error_message);
    if($validation_guard !== 1){
        $loggable["type"] = "Input_Error";
        $loggable["status"] = "Warning";
        if($validation_guard !== 0){
            $loggable["status"] = "Error";
            $loggable["exception"]["validation"] = "Invalid Validation Check, check validation code for possible bugs";
        } 
        throw new Exception("Validation", 1);
    }
    $loggable["message"]["userInput"] = $request;
    $request["default"]["equipment_type"] = get_equipment_type($request["equipment_type"] , $pdo , "id");
    $sql = create_insertion_generator($request , " equipment " , "default" , 0);
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
        throw new Exception("Server_Error_CE0001");
    }
    try{
        $request["specific"]["equipment_id"] = $equipment_id;
        $sql = create_insertion_generator($request , " " . $request["equipment_type"] , "specific" , 0);
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
        throw new Exception("Server_Error_CE0002");
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
        throw new Exception("Server_Error_CE0003");
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
        $request["reference"] = array("user_id" => "1"
                        ,"group_id" => "1"
                        ,"group_id" => $equipment_id
                        ,"user_permission_level" => "0"
                        ,"status" => "1"
                        );
        $sql = create_insertion_generator($request , " users_inside_groups_equipment " , "reference" , 0);
        $statement = $pdo->prepare($sql);
        $statement->execute();

    $loggable["type"] = "Created_Equipment";
    $loggable["group_id"] = $group_id;
    $ret["server_message"] = "Equipment Created";
    $ret["message"] = get_equipment(" * " , $equipment_id , $pdo);
    throw new Exception("Equipment_Created");
}catch(Exception $e){
    log_create($ret , $success , $e , $loggable , $error_message , $pdo);
    return $ret;
}
}

?>
