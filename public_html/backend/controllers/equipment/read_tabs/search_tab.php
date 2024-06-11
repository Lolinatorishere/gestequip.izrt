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
    $equipment_types["items"] = clean_query($filter  , $equipment_types["items"]);
    $request = array("table" => "equipment");
    $default_columns = describe_table($request , $pdo);
    $default_columns["items"] = parse_equipment_type_columns($default_columns["items"]);
    $ret["users"] = $users;
    $ret["groups"] = $groups;
    $ret["equipment_defaults"] = $default_columns["items"];
    $ret["equipment_types"] = $equipment_types["items"];
    return $ret;
}

function read_request_sch($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => array(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["refresh"])){
        return on_request_sch_load($auth_groups , $data_request , $pdo , $user_id);
    }else{
        return on_request_sch_refresh($auth_groups , $data_request , $pdo , $user_id);
    }
}

?>
