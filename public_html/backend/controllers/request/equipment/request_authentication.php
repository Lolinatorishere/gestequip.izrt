<?php

function is_user_in_groups($groups){
    $guard = 0;
    $group_auth = array('auth' => 1 
                       ,'own_auth' => 1
                       ,'de_auth' => 1
    );
    foreach ($group_auth as $key => $value) {
        if(count($groups[$key]) != 0){
            $guard++;
        }
    }
    if($guard == 0){
        return 1;
    }
    return 0;
}

function check_against_auth_groups($groups){
    $auth_groups = $_SESSION["group_auth"]["auth"];
    $checked = array();
    foreach($groups as $group){
        foreach ($auth_groups as $auth_group) {
            if($auth_group !== $group["group_id"])
                continue;
            array_push($checked , $group);
        }
    }
    return $checked;
}

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

function user_group_request_authentication($request , $pdo){
    $action_auth = 0;
    $action_auth += group_auth_check($request , $pdo);
    if($action_auth !== 1)
        return 0;
    $action_auth += group_user_check($request , $pdo);
    if($action_auth !== 2)
        return 0;
    return 1;
} 


function tab_auth_handle($auth_level){
    if($auth_level === 1)
        return 1;
    if($_SESSION["user_type"] === 'Admin')
        return 1;
    if($auth_level === 2){
        if($_SESSION["user_type"] === 'Manager')
            return 1;
    }
    return 0;
}

?>
