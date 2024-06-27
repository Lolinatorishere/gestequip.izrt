<?php

function read_all_equipment($data_request , $pdo){
    if($_SESSION["user_type"] !== "Admin")
        return "Unauthorised Request";
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups_equipments "
                    ,"specific" => " user_id > 0 "
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
            return "No Equipments in Database.";
    }
    return $all_references;
}
?>
