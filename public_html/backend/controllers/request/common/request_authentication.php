<?php
function is_user_in_groups($groups){
    $guard = 0;
    $group_auth = array('auth' => 1 
                       ,'own_auth' => 1
                       ,'de_auth' => 1
    );
    foreach($group_auth as $key => $value){
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
    if(is_array($groups)){
        foreach($groups as $group){
            foreach($auth_groups as $auth_group){
                if($auth_group !== $group["group_id"])
                    continue;
                array_push($checked , $group);
            }
        }
    }else{
        foreach($auth_groups as $auth_group){
            if($auth_group !== $groups)
                continue;
            array_push($checked , $groups);
        }
    }
    if(count($checked) === 0){
        array_push($checked , 0);
    }
    return $checked;
}

function group_auth_check($request){
    if(!isset($request["group_id"]))
        return 0;
    $auth_table = $_SESSION["group_auth"];
    foreach($auth_table["auth"] as $authorised_groups){
        if($authorised_groups == $request["group_id"]){
            return 1;
        }
    }
    return 0;
}

function group_user_auth_check($request , $pdo){
    if(!isset($request["group_id"]))
        return 0;
    $group_users_request = array("fetch" => " * "
                                ,"table" => " users_inside_groups "
                                ,"specific" => " group_id = " . $request["group_id"] 
                                             . " AND " 
                                             . " group_id > 1"
                                ,"counted" => 1
                           );
    $users_in_group = get_queries($group_users_request , $pdo);
    if(count($users_in_group["items"]) <= 0)
        return 0;
    foreach ($users_in_group["items"] as $user){
        if($user["user_id"] == $_SESSION["id"]){
            if($user["user_permission_level"] < 1){
                return 0;
            }
            return 1;
        }
    }
    return 0;
}

function user_group_request_authentication($request , $pdo){
    if(!isset($request["group_id"]))
        return 0;
    if(!isset($request["user_id"]))
        return 0;
    if($_SESSION["user_type"] === "Admin"){
        return 1;
    }
    $action_auth = 0;
    $action_auth += group_auth_check($request , $pdo);
    if($action_auth !== 1)
        return 0;
    $action_auth += group_user_auth_check($request , $pdo);
    if($action_auth !== 2)
        return 0;
    return 1;
}

// what was i making here
// you were making a request to the db to get the specific equipments auth level 
function equipment_authentication($request , $pdo){
    if(!isset($request["equipment_id"]))
        return 0;
    if($_SESSION["user_type"] === "Admin"){
        return 1;
    }
    if(!isset($request["group_id"])){
        return array("error" => "error");
    }
    $auth = check_against_auth_groups($request["group_id"]);
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups_equipments "
                    ,"counted" => 1
                    ,"specific" => " user_id=" . $_SESSION["id"] 
                                 . " and group_id=" . $auth[0]
                                 . " and equipment_id =" . $request["equipment_id"]
                    );
    $user_equipment_auth = get_queries($request , $pdo);
    if($user_equipment_auth["total_items"] !== 1)
        return 0;
    if($user_equipment_auth["user_permission_level"] < 1)
        return 0;
    if($user_equipment_auth["user_permission_level"] < 2)
        return 2;
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
