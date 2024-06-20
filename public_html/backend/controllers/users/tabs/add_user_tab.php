<?php

function on_request_add_load($data_request , $pdo , $user_id){
    $data_specific = array();
    $request = array("fetch" => " * "
                    ,"table" => " user_groups "
                    ,"specific" => "id > 0"
                    );
    $groups = get_queries($request , $pdo);
    $request = array("table" => "users");
    $default_columns = describe_table($request , $pdo);
    $data_specific["items"] = parse_users_type_columns($default_columns["items"]);
    $data_specific["groups"] = $groups;
    return $data_specific;
}

function on_request_add_refresh($data_request , $pdo , $user_id){
    switch($data_request["refresh"]){
        case "groups":
        $data_specific = array("groups" => array());
        $request = array("fetch" => " * " 
                ,"table" => " user_groups "
                ,"specific" => " id > 0 "
                ,"current_page" => $data_request["page"]
                ,"limit" => 10
            );
        $manageable_groups = get_groups($request , $pdo);
        $manageable_groups["items"] = clean_query($manageable_groups["items"]);
        $data_specific["groups"] = $manageable_groups;
        return $data_specific;
        case "create":
            return create_user($data_request , $pdo);
    }
}

function read_request_add($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => array(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["refresh"])){
        return on_request_add_load($data_request , $pdo , $user_id);
    }else{
        return on_request_add_refresh($data_request , $pdo , $user_id);
    }
}


?>
