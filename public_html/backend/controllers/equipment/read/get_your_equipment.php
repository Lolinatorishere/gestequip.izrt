<?php

function read_your_equipment($data_request , $pdo){
    // what queries can data specific have:
    if(empty($_SESSION["id"]))
        return "User Not Logged in";
    if(!isset($_SESSION["group_auth"]))
        return;
    $data_request["requests"] = array();
    $request = array("fetch" => " DISTINCT * "
                    ,"table" => " users_inside_groups_equipments "
                    ,"specific" => " user_id=" . $_SESSION["id"]
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["current_page"] = $data_request["page"];
    }
    if(isset($data_request["limit"])){
        $request["limit"] = $data_request["limit"];
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
