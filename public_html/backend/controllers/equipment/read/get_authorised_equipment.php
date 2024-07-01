<?php

function read_authorised_equipment($data_request , $pdo){
    // what queries can data specific have:
    $auth_users = get_all_auth_users($data_request , $pdo)["items"];
    if(empty($_SESSION["id"]))
        return "User Not Logged in";
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_user_ids = array();
    foreach($auth_users as $key => $value){
        array_push($auth_user_ids , $value["id"]);
    }
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups_equipments "
                    ,"specific" => " user_id IN (" . sql_array_query_metacode($auth_user_ids) . ") "
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
    }
    if(isset($data_request["limit"])){
        $request["limit"] = $data_request["limit"];
    }
    if(isset($data_request["total_items"])){
        $request["total_items"] = $data_request["total_items"];
    }
    $all_references = get_queries($request , $pdo);
    $parsed_items = array();
    foreach($all_references["items"]  as $key => $value){
        $query = array("user_id" => $value["user_id"]
                      ,"group_id" => $value["group_id"]
                      ,"equipment_id" => $value["equipment_id"]
                      );
        $request = array("query" => $query);
        array_push($parsed_items , read_equipment($request , $pdo));
    }
    $all_references["items"] = $parsed_items;
    if(isset($all_references["total_items"])){
        if($all_references["total_items"] === 0)
            return "No Equipments Assigned";
    }
    return $all_references;
}
?>
