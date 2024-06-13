<?php

function equipment_search_query_parse_inputs($queries){
    $sql = "";
    $i = 1;
    $total_parameters = count($queries);
    foreach($queries as $key => $value){
        $sql .= "`". $key ."`" . " LIKE '" . $value . "%' ";
        if($i < $total_parameters){
            $sql .= " AND ";
        }
        $i++;
    }
    return $sql;
}

function equipment_search_query_get_previous_equipment_ids($db_responses , $pdo){
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
        return get_queries($request , $pdo);
    }
    // check if the table has equipment_type (should only be the equipment table)
    if(isset($previous_request["items"][0]["equipment_type"])){
        return $previous_request;
    }
    // the previous request was not a valid db_response
    return $previous_request["message"] = "error";
}

function equipment_search_query_user_group_id($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
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

function equipment_search_query_default($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    $guard = 0;
    if(!isset($queries["default_query"])){
        $info_from_server = $info_from_server;
        return;
    }
    if($page_check["paged_query"] === "default_query"){
        $page = $page_check["page"];
    }
    if(validate_search_table_inputs($queries , "default_query" , " equipment " , $pdo) !== 1){
        $info_from_server = "No Queries";
        return;
    }
    if($info_from_server === "No Queries")
        return;
    if($info_from_server === "not applicable" || $info_from_server === "unset")
        $guard = 0;
    if($info_from_server === "found queries"){
        $equipment_ids = array();
        foreach($db_responses["from_id"]["items"] as $item){
            array_push($equipment_ids , $item["equipment_id"]);
        }
        $equipment_ids_string = sql_array_query_metacode($equipment_ids);
        $guard = 1;
    }
    $string_query = equipment_search_query_parse_inputs($queries["default_query"]);
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

function equipment_search_query_equipment_type($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    $guard = 0;
    if(!isset($queries["equipment_type"])){
        $info_from_server = $info_from_server;
        return;
    }
    if($info_from_server === "No Queries")
        return;
    if($info_from_server === "unset"){
        $guard = 0;
    }
    if($info_from_server === "Found Queries"){
        $previous_request = equipment_search_query_get_previous_equipment_ids($db_responses , $pdo);
        if(isset($previous_request["message"])){
            if($previous_request["message"] === "error"){
                $info_from_server = "No Queries";
                return "error";
            }
        }
        $guard = 1;
    }
    if($page_check["paged_query"] === "equipment_type"){
        $page = $page_check["page"];
    }
    $request = array("fetch" => " * "
        ,"table" => " equipment_types "
        ,"counted" => 1
    );
    $equipment_types = get_queries($request , $pdo);
    foreach($equipment_types["items"] as $item){
        if($queries["equipment_type"] === $item["equipment_type"]){
            $specific_item = $item["id"];
            $db_responses_eq_type = $item;
        }
    }
    if(!isset($specific_item)){
        $info_from_server = "No Queries";
        return;
    }
    if($guard === 0){
        $request = array("fetch" => " * "
                        ,"table" => " equipment "
                        ,"specific" => "equipment_type=\"" . $specific_item ."\""
                        );
        if(isset($page)){
            $request["current_page"] = $page;
        }
        $db_responses["equipment_type"] = get_queries($request , $pdo);
        if($db_responses["equipment_type"]["total_items"] === 0){
            $info_from_server = "No Queries";
            return;
        }
    }else{
        $equipment_ids = array();
        foreach($previous_request["items"] as $item){
            array_push($equipment_ids , $item["id"]);
        }
        $equipment_ids_string = sql_array_query_metacode($equipment_ids);
        $request = array("fetch" => " * "
                        ,"table" => " equipment "
                        ,"specific" => "id IN (" . $equipment_ids_string . ") AND equipment_type=\"" . $specific_item ."\""
                        );
        if(isset($page)){
            $request["current_page"] = $page;
        }
        $db_responses["equipment_type"] = get_queries($request , $pdo);
        if($db_responses["equipment_type"]["total_items"] === 0){
            $info_from_server = "No Queries";
            return;
        }
    }
    $info_from_server = "Found Queries";
    $db_responses["equipment_type"]["equipment_type"] = $db_responses_eq_type;
}

function equipment_search_query_specific($queries , &$db_responses , $pdo , $page_check , &$info_from_server){
    $guard = 0;
    if(!isset($queries["equipment_type"]) || !isset($queries["specific_query"]))
        return;
    if($info_from_server === "No Queries")
        return;
    // todo Please for the love of god fix this buffalo buffalobuffalobuffalobuffalobuffalobuffalobuffalobuffalobuffalo
    // situation
    if(validate_search_table_inputs($queries , "specific_query" , $db_responses["equipment_type"]["equipment_type"]["equipment_type"] . "s " , $pdo) !== 1){
        $info_from_server = "No Queries";
        return;
    }
    if($info_from_server === "Found Queries"){
        $previous_request = $db_responses["equipment_type"];
        $guard = 1;
    }
    if($guard === 0)
        return;
     if($page_check["paged_query"] === "equipment_type"){
        $page = $page_check["page"];
    }
    $equipment_ids = array();
    foreach($previous_request["items"] as $item){
        array_push($equipment_ids , $item["id"]);
    }
    $equipment_ids_string = sql_array_query_metacode($equipment_ids);
    $string_query = equipment_search_query_parse_inputs($queries["specific_query"]);
    $request = array("fetch" => " * "
                    ,"table" => $db_responses["equipment_type"]["equipment_type"]["equipment_type"] . "s "
                    ,"specific" => " equipment_id IN (" . $equipment_ids_string . ") AND (" . $string_query . ") "
                    );
    if(isset($page)){
        $request["current_page"] = $page;
    }
    $db_responses["specific_query"] = get_queries($request , $pdo);
    if($db_responses["specific_query"]["total_items"] === 0){
        $info_from_server = "No Queries";
        return;
    }
    $info_from_server = "Found Queries";
}


function equipment_search_query($queries , $pdo , $page_check){
    $info_from_server = "unset";
    $ret = array("message" => "Server Error"); 
    $db_responses = array();
    $parsed_search;
    equipment_search_query_user_group_id($queries , $db_responses ,  $pdo , $page_check , $info_from_server);
    equipment_search_query_default($queries , $db_responses , $pdo , $page_check , $info_from_server);
    equipment_search_query_equipment_type($queries , $db_responses , $pdo , $page_check , $info_from_server);
    equipment_search_query_specific($queries , $db_responses , $pdo , $page_check , $info_from_server);
    if($info_from_server === "No Queries"){
        $ret["message"] = $info_from_server;
    }
    if($info_from_server === "unset"){
        $ret["message"] = "No Parameters Were Inserted";
    }
    if($info_from_server === "Found Queries"){
        $total_responses = count($db_responses);
        $i = 1;
        foreach($db_responses as $response){
            if($i === $total_responses){
                $parsed_search = $response;
            }
            $i++;
        }
        $ret["message"] = "Found " . $parsed_search["total_items"];
        if($parsed_search["total_items"] !== 1){
            $ret["message"] .= " queries";
        }else{
            $ret["message"] .= " query";
        }
        $ret["result"] = $parsed_search;
    }
    return $ret;
}

function equipment_search($data_request , $pdo){
    $search_queries = $data_request["query"];
    $user_group_query_check = 0;
    $server_message = array("server_message" => "Error"
                           ,"message" => "No Parameters Were Inserted"
                           );
    $total_queries = count($search_queries);
    $query = array();
    $page = array("paged_query" => ""
        ,"page" => $data_request["page"]
    );
    if($total_queries === 0)
        return $server_message;
    if(isset($search_queries["user_id"])){
        $query["user_id"] = preg_replace('/[^0-9]/s' , '' , $search_queries["user_id"]);
        $page ["paged_query"] = "both_id";
    }
    if(isset($search_queries["group_id"])){
        $query["group_id"] = preg_replace('/[^0-9]/s' , '' , $search_queries["group_id"]);
        $page ["paged_query"] = "both_id";
    }
    if(isset($search_queries["default"])){
        $query["default_query"] = $search_queries["default"];
        $page ["paged_query"] = "default_query";
    }
    if(isset($search_queries["equipment_type"])){
        $query["equipment_type"] = preg_replace('/[^a-zA-Z]/s' , '' , $search_queries["equipment_type"]);
        $page ["paged_query"] = "equipment_type";
        if(isset($search_queries["specific"])){
            if(count($search_queries["specific"]) !== 0){
                $query["specific_query"] = $search_queries["specific"];
                $page["paged_query"] = "specific_query";
            }
        }
    }
    if(!isset($data_request["total_items"])){
        $page["paged_query"] = "unset";
    }
    $response = equipment_search_query($query , $pdo , $page);
    $server_message["message"] = $response["message"];
    if(isset($response["result"])){
        $server_message["server_message"] = $response["message"];
        $server_message["message"] = $response["result"];
    }
    return $server_message;
}
?>
