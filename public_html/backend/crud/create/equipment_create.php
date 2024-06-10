<?php

function group_auth_check($request){
    $auth_table = $_SESSION["group_auth"];
    foreach($auth_table["auth"] as $authorised_groups){
        if($authorised_groups == $request["group_id"]){
            return 1;
        }
    }
    return 0;
}

function group_user_check($request , $pdo){
    $group_users_request = array("fetch" => " * "
                                ,"table" => " users_inside_groups "
                                ,"specific" => " group_id = " . $request["group_id"]
                                ,"counted" => 1
                           );
    $users_in_group = get_queries($group_users_request , $pdo);
    if(count($users_in_group["items"]) <= 0)
        return 0;
    foreach ($users_in_group["items"] as $user){
        if($user["user_id"] == $request["user_id"]){
            return 1;
        }
    }
    return 0;
}

function equipment_create_request_authentication($request , $pdo){
    $action_auth = 0;
    $action_auth += group_auth_check($request , $pdo);
    if($action_auth !== 1)
        return 0;
    $action_auth += group_user_check($request , $pdo);
    if($action_auth !== 2)
        return 0;
    return 1;
} 

function validate_table_inputs($request , $check , $db_table , $pdo){
    $table_check = 0;
    $table_request = array("table" => $db_table);
    $table = describe_table($table_request , $pdo);
    $counted_table = count($request[$check]);
    foreach($request[$check] as $key => $value){
        for ($i = 0; $i < count($table["items"]) ; $i++){ 
            try{
                if($table["items"][$i]["Field"] !== $key)
                    continue;
                if(preg_match('/[<>\'`\/\\\\_]/' , $request[$check][$key]))
                    continue;
                if($table["items"][$i]["Key"] === "UNI"){
                    $unique_request = array("fetch" => " " . $key . " "
                                           ,"table" => $db_table
                                           ,"counted" => 1
                                           ,"specific" => " " . $key . "='" . $value . "'"
                                        );
                    $unique = get_queries($unique_request , $pdo);
                    if(count($unique["items"]) >= 1)
                        return 0;
                }
                if($table["items"][$i]["Null"] === "NO"){
                    if(is_null($value))
                        return 0;
                }
                if($table["items"][$i]["Type"] === "tinyint(1)"){
                    if($request[$check][$key] !== false && $request[$check][$key] !== true)
                        return 0;
                }
                    if($table["items"][$i]["Type"] === "date"){
                        list($year , $month , $day) = explode('-', $request[$check][$key]);
                        if(!checkdate($month , $day , $year))
                            return 0;
                    }
            }catch(TypeError $e){
                error_log(print_r($e , true));
                return 0;
            }
            $table_check++;
        }
    }
    if($table_check !== $counted_table)
        return 0;
    return 1;
}

function equipment_create_request_validation($request , $pdo){
    if(!isset($request["default"]))
        return 0;
    if(!isset($request["specific"]))
        return 0;
    if(!isset($request["user_id"]))
        return 0;
    if(!isset($request["group_id"]))
        return 0;
    if(!isset($request["group_id"]))
        return 0;
    if(validate_table_inputs($request , "default" , " equipment " , $pdo) === 0)
        return 0;
    if(validate_table_inputs($request , "specific" , " " . $request["equipment_type"] . "s " , $pdo) === 0)
        return 0;
}

function get_equipment_type_id($equipment_type , $pdo){
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types "
                    ,"counted" => 1
                    ,"specific" => " equipment_type='" . $equipment_type . "' "
                    );
    $query = get_query($request , $pdo);
    return $query["items"]["id"];
}

function create_equipment_create_query($request , $db_table , $input_type){
    error_log(print_r($request[$input_type] , true));
    $values = array();
    $columns = array();
    $total_specific_inputs = count($request[$input_type]);
    foreach($request[$input_type] as $key => $value){
        $column = "`" . $key . "`";
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

function create_equipment_users_groups_query($request , $equipment_id){
    $columns = array(" `user_id`, `group_id`, `equipment_id`, `user_permission_level`, `status`");
    $values = array();
    $eq_id = " '" . $equipment_id . "' ";
    $us_id = " '" . $request["user_id"] . "' ";
    $gp_id = " '" . $request["group_id"] . "' ";
    if(!isset($request["user_permission_level"])){
        $perm_lvl = " '1' ";
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
    $loggable = array("type" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"user_id" => $_SESSION["id"]
                     );
    $loggable["message"]["userInput"] = $request;
    if(equipment_create_request_validation($request , $pdo) === 0)
        throw new Exception("Blocked Invalid Inputs");
    if(equipment_create_request_authentication($request , $pdo) === 0)
        throw new Exception("Blocked Unuthorised Creation");
    $request["default"]["equipment_type"] = get_equipment_type_id($request["equipment_type"] , $pdo);
    $sql = create_equipment_create_query($request , " equipment " , "default");
    try{
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $equipment_id = $pdo->lastInsertId();
        $loggable["message"]["default_sql"] = $sql;
        $loggable["message"]["default_inserted_id"] = $equipment_id;
    }catch(PDOException $e){
        array_push($loggable["exception"] , $e);
        throw new Exception("Server Error");
    }
    try{
        $request["specific"]["equipment_id"] = $equipment_id;
        $sql = create_equipment_create_query($request , " " . $request["equipment_type"] . "s " , "specific");
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $specifics_id = $pdo->lastInsertId();
        $loggable["message"]["specific_sql"] = $sql;
        $loggable["message"]["specific_inserted_id"] = array("type_id" => $specifics_id
                                                 ,"eq_type" => $request["equipment_type"]
                                                 );
    }catch(PDOException $e){
        array_push($loggable["exception"] , $e->getMessage());
        $deletion_request = array("table" => " equipment "
                                 ,"specific" => "id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        throw new Exception("Server Error");
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
        array_push($loggable["exception"] , $e->getMessage());
        $deletion_request = array("table" => $request["equipment_type"] ."s "
                                 ,"specific" => "equipment_id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        $deletion_request = array("table" => " equipment "
                                 ,"specific" => "id=" . $equipment_id
                                 );
        delete_equipment($deletion_request , $pdo);
        throw new Exception("Server Error");
    }
    try{
        $sql = create_equipment_users_groups_query($request , $equipment_id);
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $loggable["message"]["users_inside_groups_equipments_ids"] = array($request["user_id"] , $request["group_id"] , $equipment_id);
        $loggable["message"]["users_inside_groups_equipments_sql"] = $sql;
    }catch(PDOException $e){
        array_push($loggable["exception"] , $e->getMessage());
        throw new Exception("equipment_Group query not made");
    }
    throw new Exception("Equipment Created");
}catch(Exception $e){
    switch($e->getMessage()){
        case 'Equipment Created':
            $loggable["type"] = "Equipment_Create";
            $loggable["equipment_id"] = $equipment_id;
            create_log($loggable , "equipment" , $pdo);
            $ret["server_message"] = "Equipment Created";
            // todo equipment querying seems to not be working
            $ret["message"] = get_equipment(" * " , $equipment_id , $pdo);  
            return $ret;
        default:
            $loggable["type"] = "user_input_error";
            if(isset($equipment_id)){
                $loggable["equipment_id"] = $equipment_id;
            }
            $loggable["user_inputs"] = $request;
            create_log($loggable , "equipment" , $pdo);
            $ret["server_message"] = "Opperation could not Be Completed";
            $ret["message"] = $e->getMessage();
            return $ret;
    }
}
}

?>
