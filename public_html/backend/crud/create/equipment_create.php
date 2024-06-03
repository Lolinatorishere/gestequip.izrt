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

function equipment_create_request_validation($request , $pdo){
    $table_check = 0;
    $table_request = array("table" => " " . $request["equipment_type"] . "s ");
    $table = describe_table($table_request , $pdo);
    $counted_table = count($request["specific"]);
    foreach($request["specific"] as $key => $value){
        for ($i=0; $i < count($table["items"]) ; $i++) { 
            if($table["items"][$i]["Field"] === $key){
                $table_check++;
                break;
            }
        }
    }
    if($table_check !== $counted_table)
        return 0;
    return 1;
}

function create_equipment($request , $pdo){
    $insert_error = "User not created";
    if(equipment_create_request_validation($request , $pdo) === 0)
        return $insert_error;
    if(equipment_create_request_authentication($request , $pdo) === 0)
        return $insert_error;
    error_log("authorised lmao");
    return "to do create the actual return lol";
}
?>
