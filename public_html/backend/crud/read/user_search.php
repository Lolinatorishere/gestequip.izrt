<?php

function user_search_query($query , $pdo , $page){
    $guard = 0;
    if(validate_external_search_inputs($queries , "user_query" , " users " , $pdo) !== 1){
        $info_from_server = "No Queries";
        return;
    }
    if($info_from_server === "Found Queries"){
        $previous_request = $db_responses["equipment_type"];
        $guard = 1;
    }
    if($guard === 0)
        return;
    if($page_check["paged_query"] === "user_query"){
        $page = $page_check["page"];
    }
    $equipment_ids = array();
    foreach($previous_request["items"] as $item){
        array_push($equipment_ids , $item["id"]);
    }
    $equipment_ids_string = sql_array_query_metacode($equipment_ids);
    $string_query = equipment_search_query_parse_inputs($queries["specific_query"]);
    $request = array("fetch" => " * "
                    ,"table" => $db_responses["equipment_type"]["equipment_type"]["equipment_type"]
                    ,"specific" => " equipment_id IN (" . $equipment_ids_string . ") AND (" . $string_query . ") "
                    );
    if(isset($page)){
        $request["current_page"] = $page;
    }
    $db_responses["user_query"] = get_queries($request , $pdo);
    if($db_responses["specific_query"]["total_items"] === 0){
        $info_from_server = "No Queries";
        return;
    }
    $info_from_server = "Found Queries";

}

function user_search($data_request , $pdo){
    $search_queries = $data_request["query"];
    $user_group_query_check = 0;
    $server_message = array("server_message" => "Error"
        ,"message" => "No Parameters Were Inserted"
    );
    $total_queries = count($search_queries);
    $query = array();
    if($total_queries === 0)
        return $server_message;
    if(!isset($search_queries["user_id"])){
        $query["user_id"] = preg_replace('/[^0-9]/s' , '' , $search_queries["user_id"]);
    }
    $response = user_search_query($query , $pdo , $page);
    $server_message["message"] = $response["message"];
    if(isset($response["result"])){
        $server_message["server_message"] = $response["message"];
        $server_message["message"] = $response["result"];
    }
    return $server_message;
}

?>
