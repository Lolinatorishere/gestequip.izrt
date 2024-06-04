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

function create_equipment_create_query($request , $input_type){
    $values = array();
    $columns = array();
    $total_specific_inputs = count($request[$input_type]);
    foreach($request[$input_type] as $key => $value){
        $column = "'" . $key . "'";
        $input = "'" . $value . "'";
        array_push($columns, $column);
        array_push($values, $input);
    }
    if(count($columns) !== count($values))
        return 0;
    $create_request = array("multiple" => 1
                           ,"table" => " " . $request["equipment_type"] . "s "
                           ,"columns" => $columns
                           ,"values" => $values
                           );
    return common_insert_query($create_request);
}

function create_equipment($request , $pdo){
    $insert_error = "User not created";
    if(equipment_create_request_validation($request , $pdo) === 0)
        return $insert_error;
    if(equipment_create_request_authentication($request , $pdo) === 0)
        return $insert_error;
    error_log("authorised and valid lmao");
    $request["default"]["equipment_type"] = get_equipment_type_id($request["equipment_type"] , $pdo);
    $sql = create_equipment_create_query($request , "default");
    error_log($sql);
}

?>
