<?php

function search_query_create_get_previous_equipment_ids($db_responses , $pdo){
    $equipment_ids = array();
    $previous_request;
    $parsed_previous_request;
    $i = 1;
    // how many times have you Found Queries :hmmmBusiness:
    $all_requests = count($db_responses);
    foreach ($db_responses as $key => $value){
        if($i === $all_requests){
            $previous_request = $value;
        }
        $i++;
    }
    //if the previous request has equipment_id it means its on the
    //users_inside_groups_equipments table which is not good
    if(isset($previous_request["items"][0]["equipment_id"])){
        foreach($previous_request["items"] as $item){
            array_push($equipment_ids , $item["equipment_id"]);
        }
        $equipment_ids_string = sql_array_query_metacode($equipment_ids);
        $request = array("fetch" => " * "
                        ,"table" => " equipment "
                        ,"specific" => " id IN ( " . $equipment_ids_string . " )"
                        );
        $parsed_previous_request = get_queries($request , $pdo);
    }
    // check if the table has equipment_type (should only be the equipment table)
    if(isset($previous_request["items"][0]["equipment_type"])){
        return $previous_request;
    }
    // the previous request was not a valid db_response
    return $previous_request["message"] = "error";
}

function search_query_create_user_group_id($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    if($page_check["paged_query"] === "both_id"){
        $page = $page_check["page"];
    }
    if(isset($queries["user_id"]) && isset($queries["group_id"])){
        $request = array("fetch" => " * "
                        ,"table" => " users_inside_groups_equipments"
                        ,"specific" => " user_id = \"" . $queries["user_id"] . "\" and group_id =\"". $queries["group_id"] . "\""
                        );
        if(isset($page)){
            $request["current_page"] = $page;
        }
        $db_responses["from_id"] = get_queries($request , $pdo);
    }else{
        if(isset($queries["user_id"])){
            $request = array("fetch" => " * "
                           ,"table" => " users_inside_groups_equipments"
                           ,"specific" => " user_id = \"" . $queries["user_id"] . "\""
                           );
            if(isset($page)){
                $request["current_page"] = $page;
            }
            $db_responses["from_id"] = get_queries($request , $pdo);
        }
        if(isset($queries["group_id"])){
            $request = array("fetch" => " * "
                           ,"table" => " users_inside_groups_equipments"
                           ,"specific" => " group_id =\"". $queries["group_id"] . "\""
                           );
            if(isset($page)){
                $request["current_page"] = $page;
            }
            $db_responses["from_id"] = get_queries($request , $pdo);
        }
    }
    if(!isset($db_responses["from_id"])){
        $info_from_server = "not applicable";
        $db_responses["from_id"]["total_items"] = 0;
        return;
    }
    if($db_responses["from_id"]["total_items"] === 0){
        $info_from_server = "No Queries";
        return;
    }
    $info_from_server = "Found Queries";
    return;
}

function search_query_parse_default_inputs($queries){
    $sql = "";
    $i = 1;
    $total_parameters = count($queries);
    foreach($queries as $key => $value){
        $sql .= $key . " LIKE '" . $value . "%' ";
        if($i < $total_parameters){
            $sql .= " AND ";
        }
        $i++;
    }
    return $sql;
}

function search_query_create_default($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    if($page_check["paged_query"] === "default_query"){
        $page = $page_check["page"];
    }
    $guard = 0;
    if(!isset($queries["default_query"])){
        $db_responses["default_query"]["response"] = "not applicable";
        return $ret;
    }
    if(validate_search_table_inputs($queries , "default_query" , " equipment " , $pdo) !== 1){
        $db_responses["default_query"]["response"] = "No Queries";
        return;
    }
    if($info_from_server === "No Queries")
        return;
    if($info_from_server === "not applicable" || $info_from_server === "unset")
        $guard = 0;
    if($info_from_server === "Found Queries"){
        $equipment_ids = array();
        foreach($db_responses["from_id"]["items"] as $item){
            array_push($equipment_ids , $item["equipment_id"]);
        }
        $equipment_ids_string = sql_array_query_metacode($equipment_ids);
        $guard = 1;
    }
    $string_query = search_query_parse_default_inputs($queries["default_query"]);
    if($guard == 1){
        $specific_string = " id IN(" . $equipment_ids_string . ") AND (" . $string_query . ")";
    }else{
        $specific_string = $string_query;
    }
    $request = array("fetch" => " * "
                    ,"table" => " equipment "
                    ,"specific" => $specific_string
                    );
    if(isset($page)){
        $request["current_page"] = $page_check["page"];
    }
    $db_responses["default_query"] = get_queries($request , $pdo);
    if($db_responses["default_query"]["total_items"] === 0){
        $info_from_server = "No Queries";
        return;
    }
    $info_from_server = "Found Queries";
}

function search_query_create_equipment_type($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    if($page_check["paged_query"] === "equipment_type"){
        $page = $page_checl["page"];
    }
    $guard = 0;
    if($info_from_server === "No Queries"){
        return;
    }
    if($info_from_server === "unset"){
        $guard = 0;
    }
    if($info_from_server === "Found Queries"){
        $previous_request = search_query_create_get_previous_equipment_ids($db_responses , $pdo);
        if($previous_request["message"] === "error"){
            $info_from_server = "No Queries";
            return "error";
        }
        $guard = 1;
    }
}

function search_query_create_specific($queries , $db_responses , $pdo , $page_check){
    return "stuff";
}

function search_query_create($queries , $pdo , $page_check){
    $info_from_server = "unset";
    $db_responses = array();
    search_query_create_user_group_id($queries , $db_responses ,  $pdo , $page_check , $info_from_server);
    search_query_create_default($queries , $db_responses , $pdo , $page_check , $info_from_server);
    search_query_create_equipment_type($queries , $db_responses , $pdo , $page_check , $info_from_server);
    search_query_create_specific($queries , $db_responses , $pdo , $page_check , $info_from_server);
}

function search_query($data_request , $pdo){
    $search_queries = $data_request["query"];
    $user_group_query_check = 0;
    $server_message = array("server_message" => "Error"
                           ,"message" => "No Parameters Were Inserted"
                           );
    $total_queries = count($search_queries);
    $query = array();
    if($total_queries === 0)
        return $server_message;
    if(isset($search_queries["user_id"])){
        $query["user_id"] = preg_replace('/[^0-9]/s' , '' , $search_queries["user_id"]);
        $page = array("paged_query" => "both_id"
                     ,"page" => $data_request["page"]
                     );
    }
    if(isset($search_queries["group_id"])){
        $query["group_id"] = preg_replace('/[^0-9]/s' , '' , $search_queries["group_id"]);
        $page = array("paged_query" => "both_id"
                     ,"page" => $data_request["page"]
                     );
    }
    if(isset($search_queries["default"])){
        $query["default_query"] = $search_queries["default"];
        $page = array("paged_query" => "default_query"
                     ,"page" => $data_request["page"]
                     );
    }
    if(isset($search_queries["equipment_type"])){
        $equipment_type = preg_replace('/[^a-zA-Z]/s' , '' , $search_queries["equipment_type"]);
        $page = array("paged_query" => "equipment_type"
                     ,"page" => $data_request["page"]
                     );
        if(isset($search_queries["specific"])){
            if(count($search_queries["specific"]) !== 0){
                $query["specific_query"] = $search_queries["specific"];
                $page = array("paged_query" => "specific_query"
                             ,"page" => $data_request["page"]
                             );
            }
        }
    }
    $response = search_query_create($query , $pdo , $page);
    if($response !== " Found Queries"){
        $server_message["message"] = $response["response"];
        return $server_message;
    }
}
?>
