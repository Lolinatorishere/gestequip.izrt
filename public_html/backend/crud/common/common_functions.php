<?php

function clean_query($filter , $items){
    $washed_items = array();
    foreach($items as $item){
        array_push($washed_items , merge_arrays($filter , $item));
    } 
    return $washed_items;
}

function custom_query_filter($number){
    $filter = array("filter" => array());
    for($i = 0 ; $i < $number/2 ; $i++){
        $filter["filter"][$i] = $i;
    }
    return $filter;
}

function parse_equipment_type_columns($columns){
    $parsed_columns = array();
    $i = 1;
    $filter = custom_query_filter(count($columns));
    foreach($columns as $column){
        if($column["Key"] === "PRI")
            continue;
        if($column["Key"] === "MUL")
            continue;
        if(isset($column["Default"]))
            continue;
        if($column["Field"] === "id")
            continue;
        if($column["Field"] === "equipment_id")
            continue;
        if($column["Field"] === "registration_date")
            continue;
        if($column["Field"] === "registration_lock")
            continue;
        if($column["Field"] === "equipment_type")
            continue;
        if($column["Field"] === "serial_brand_md5")
            continue;
        $column = merge_arrays($filter , $column);
        array_push($parsed_columns , $column);
    }
    return $parsed_columns;
}


function page_check(&$request){
    if(!isset($request["total_pages"])){
        $request["total_pages"] = 1;
    }
    if(!isset($request["page"])){
        $request["page"] = 1;
    }
    if($request["total_pages"] <= 0){
        $request["total_pages"] = 1;
    }
    if($request["page"] <= 0){
        $request["page"] = 1;
    }
    return;
}

function get_pre_altered_equipment_information($data_request , $pdo){
    $previous_info = array();
    if(isset($data_request["default"])){
        $request = array("fetch" => " * "
                         ,"table" => " equipment "
                         ,"counted" => 1
                         ,"specific" => "id=\"" . $data_request["equipment_id"] . "\""
                     );
        $previous_info["default"] = get_query($request , $pdo)["items"];
    }
    if(isset($data_request["default"])){
        $request = array("fetch" => " * "
            ,"table" => $data_request["equipment_type"]
            ,"counted" => 1
            ,"specific" => "equipment_id=\"" . $data_request["equipment_id"] . "\""
        );
        $previous_info["specific"] = get_query($request , $pdo)["items"];
    }
    if(isset($data_request["user_permission_level"])){
        printLog("lasdnfasdjhf");
        $request = array("fetch" => " * "
                      ,"table" => " users_inside_groups_equipments "
                      ,"counted" => 1
                      ,"specific" => " equipment_id=" . $data_request["equipment_id"]
                                     . " group_id=" . $data_request["group_id"]
                                     . " user_id=" . $data_request["user_id"]
                      );
        $previous_info["user_group_equipment"] = get_query($request , $pdo)["items"];
    }
    return $previous_info;
}



function printLog($log){
    error_log(print_r($log,true));
}

?>
