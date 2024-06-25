<?php

function on_request_sch_load($auth_groups , $data_request , $pdo , $user_id){
    $limit = 8;
    $i = 0;
    $ret = array("users" => ""
                ,"groups" => ""
                ,"search_table" => ""
                );
    $users = array();
    $groups = array("items" => array());
    if(!isset($data_request["limit"])){
        $data_request["limit"] = $limit;
    }
    if($_SESSION["user_type"] !== "Admin"){
        $users = get_all_auth_users($data_request , $pdo);
    }else{
        $request = array("fetch" => " id , username , users_name , email , phone_number , regional_indicator , date_created , account_status"
                        ,"table" => " users "
                        ,"counted" => 1
                        ,"specific" => " id > 1 " 
                        );
        $users = get_queries($request , $pdo);
    }
    $ret["users"] = $users;
    if($_SESSION["user_type"] !== "Admin"){
        $ret["groups"] = $auth_groups;
    }else{
        $request = array("fetch" => " * "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => " id > 1 " 
                        );
        $ret["groups"] = get_queries($request , $pdo);
    }
    $request = array("table" => " user_groups ");
    $user_table = describe_table($request , $pdo);
    $return_table = array();
    foreach ($user_table["items"] as $key => $value) {
        if($value["Field"] === "id"||
           $value["Field"] === "pass"||
           $value["Field"] === "date_created"){
            continue;
        }
        array_push($return_table , $value);
    }
    $ret["search_table"] = $return_table;
    return $ret;
}

function refresh_get_users_groups($data_request , $pdo){
    $limit = $data_request["limit"];
    $offset = ($data_request["page"]-1) * $limit;
    $accepted_groups = array();
    $auth_request = array();
    $request_id = preg_replace('/[^0-9]/s' , '' , $data_request["origin"]); 
    $group_request = array("fetch" => " group_id "
                    ,"table" => " users_inside_groups "
                    ,"counted" => 1
                    ,"specific" => " user_id=\"" . $request_id . "\""
                    );
    $groups = get_queries($group_request , $pdo);
    if($_SESSION["user_type"] !== "Admin"){
        $auth_groups = check_against_auth_groups($groups["items"]);
    }else{
        $auth_groups = $groups["items"];
    }
    $total_items = count($auth_groups);
    for($i = 0 ; $i < $total_items ; $i++){
        $group_id = $auth_groups[$i]["group_id"];
        $request = array("fetch" => " id , group_name "
            ,"table" => " user_groups "
            ,"counted" => 1
            ,"specific" => " id=\"" . $group_id . "\" and id > 1"
        );
        if($i >= $limit )
            break;
        $group_info = get_query($request , $pdo)["items"];
        if(empty($group_info))
            continue;
        array_push($accepted_groups , $group_info);
    }
    $ret["items"] = $accepted_groups;
    $ret["total_items"] = $total_items;
    $ret["pages"] = ceil($total_items/$limit);
    $ret["page"] = $data_request["page"];
    $ret["counted"] = 1;
    return $ret;
}

function refresh_get_groups_users($auth_groups , $data_request , $pdo){
    foreach ($auth_groups as $auth) {
        if($auth == $data_request["origin"]){
            $guard = 0;
        }
    }
    if($_SESSION["user_type"] === "Admin"){
        $guard = 0;
    }
    if(!isset($guard))
        return "none";
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => " group_id = " . $data_request["origin"] . " AND group_id > 1"
                    ,"limit" => 8
                    ,"user_fetch" => " id , users_name "
                );
    return get_users($request , $pdo);
}

function on_request_sch_refresh($auth_groups , $data_request , $pdo , $user_id){
    $ret = array();
    switch($data_request["refresh"]){
        case 'user': // loads the specific users groups 
            return refresh_get_groups_users($auth_groups , $data_request , $pdo);
        case 'group': // loads the specific groups users
            return refresh_get_users_groups($data_request , $pdo);
        case 'query':
            return group_search($data_request , $pdo);
        case 'clear':
            return on_request_sch_load($auth_groups , $data_request , $pdo , $user_id);
        default: 
            return "Server error";
    }
}

function read_request_sch($data_request , $pdo , $user_id){
    // what queries can data specific have:
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["limit"])){
        $data_request["limit"] = 8;
    }
    if(!isset($data_request["refresh"])){
        return on_request_sch_load($auth_groups , $data_request , $pdo , $user_id);
    }else{
        return on_request_sch_refresh($auth_groups , $data_request , $pdo , $user_id);
    }
}



?>
