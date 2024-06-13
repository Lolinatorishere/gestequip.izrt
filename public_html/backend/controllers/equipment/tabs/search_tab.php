<?php

function on_request_sch_load($auth_groups , $data_request , $pdo , $user_id){
    $limit = 8;
    $i = 0;
    $ret = array("users" => ""
                ,"groups" => ""
                ,"equipment_types" => ""
                ,"equipment_defaults" => ""
                );
    $users = array();
    $groups = array("items" => array());
    if(!isset($data_request["limit"])){
        $data_request["limit"] = $limit;
    }
    $users = get_all_auth_users($data_request , $pdo);
    foreach($auth_groups as $auth_group){
        if($i > $limit-1)
            break;
        $request = array("fetch" => " id , group_name "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => "id=" . $auth_group
                        );
        array_push($groups["items"] , get_query($request , $pdo)["items"]);
    }
    $groups["pages"] = ceil(count($auth_groups)/$limit);
    $groups["curent_page"] = 1;
    $groups["paging"] = 1;
    $groups["total_items"] = count($auth_groups);
    $request = array("fetch" => " * " 
                    ,"table" => " equipment_types "
                    ,"counted" => 1
                    );
    $equipment_types = get_queries($request , $pdo);
    $equipment_types["items"] = clean_query($filter , $equipment_types["items"]);
    $request = array("table" => "equipment");
    $default_columns = describe_table($request , $pdo);
    $default_columns["items"] = parse_equipment_type_columns($default_columns["items"]);
    $ret["users"] = $users;
    $ret["groups"] = $groups;
    $ret["equipment_defaults"] = $default_columns["items"];
    $ret["equipment_types"] = $equipment_types["items"];
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
    $auth_groups = check_against_auth_groups($groups["items"]);
    $total_items = count($auth_groups);
    for($i = 0 ; $i + $offset < $total_items ; $i++){
        $ioff = $i + $offset;
        $group_id = $auth_groups[$ioff]["group_id"];
        $request = array("fetch" => " id , group_name "
            ,"table" => " user_groups "
            ,"counted" => 1
            ,"specific" => " id=\"" . $group_id . "\" "
        );
        if($i > $limit + $offset)
            break;
        array_push($accepted_groups , get_query($request , $pdo)["items"]);
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
    if(!isset($guard))
        return "none";
    $data_specific = array("users" => array());
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => " group_id = " . $data_request["origin"]
                    ,"limit" => 8
                    ,"user_fetch" => " id , users_name "
                );
    return get_users($request , $pdo);
}

function on_request_sch_refresh($auth_groups , $data_request , $pdo , $user_id){
    $ret = array();
    switch($data_request["refresh"]){
        case 'user': // loads the specific users groups 
            return refresh_get_users_groups($data_request , $pdo);
        case 'group': // loads the specific groups users
            return refresh_get_groups_users($auth_groups , $data_request , $pdo);
        case 'type_specific':
            if(!isset($_SESSION["equipment_types"]))
                break;
            foreach($_SESSION["equipment_types"] as $type) {
                if($data_request["origin"] === $type["equipment_type"]){
                    $guard = 0;
                    break;
                }
            }
            if(!isset($guard))
                break;
            $data_specific = array("types_specific" => array());
            $request = array("table" => $data_request["origin"] . "s ");
            $columns = describe_table($request , $pdo);
            $columns["items"] = parse_equipment_type_columns($columns["items"]);
            return $columns;
        case 'query':
            return equipment_search($data_request , $pdo);
        case 'clear':
            return on_request_sch_load($auth_groups , $data_request , $pdo , $user_id);
        default: 
            return "Server error";
    }
}

function read_request_sch($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => array(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["limit"])){
        $data_request["limit"] = 8;
    }
    if(!isset($data_request["page"])){
        $data_request["page"] = 1;
    }
    if(!isset($data_request["refresh"])){
        return on_request_sch_load($auth_groups , $data_request , $pdo , $user_id);
    }else{
        return on_request_sch_refresh($auth_groups , $data_request , $pdo , $user_id);
    }
}

?>
