<?php

function group_search_query($data_request , $pdo , $page){
    $guard = 0;
    $error_message = array();
    if(validate_external_search_inputs($data_request , "query" , " user_groups " , $pdo ) !== 1){
        $ret["message"] = "Invalid Input in Query";
        return $ret;
    }
    $auth_ids = array();
    $string_query = search_query_parse_inputs($data_request["query"]);
    $request = array("fetch" => " * "
                    ,"table" => " user_groups "
                    ,"specific" => " id IN(" .  sql_array_query_metacode($_SESSION["group_auth"]["auth"]) . ") AND (" . $string_query . ") AND id > 1"
                    );
    if($_SESSION["user_type"] === "Admin"){
        $request["specific"] = $string_query . "AND id > 1";
    }
    $response = get_queries($request , $pdo);
    if($response["total_items"] === 0){
        $ret["message"] = "No Queries";
        return $ret;
    }
    $ret["message"] = "Found " . $response["total_items"];
    if($response["total_items"] !== 1){
        $ret["message"] .= " queries";
    }else{
        $ret["message"] .= " query";
    }
    $ret["result"] = $response;
    return $ret;
}

function group_search($data_request , $pdo){
    $user_group_query_check = 0;
    $server_message = array("server_message" => "Error"
        ,"message" => "No Parameters Were Inserted"
    );
    $page = $data_request["page"];
    $response = group_search_query($data_request , $pdo , $page);
    $server_message["message"] = $response["message"];
    if(isset($response["result"])){
        $server_message["server_message"] = $response["message"];
        $server_message["message"] = $response["result"];
    }
    return $server_message;
}

?>
