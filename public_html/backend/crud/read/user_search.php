<?php

function user_search_query($data_request , $pdo , $page){
    $guard = 0;
    $error_message = array();
    $ret = array();
    if(isset($_POST["query"]["email"])){
        if(preg_match('/[<>\'`\/\\\\_]/' , $_POST["query"]["email"])){
            $ret["message"] = "Invalid Email Requested";
            return $ret;
        }
        $data_request["query"]["email"] = $_POST["query"]["email"];
    }
    if(validate_external_search_inputs($data_request , "query" , " users " , $pdo ) !== 1){
        $ret["message"] = "Invalid Input in Query";
        return $ret;
    }
    $auth_users = get_all_auth_users($data_request , $pdo);
    $auth_ids = array();
    foreach ($auth_users["items"] as $key => $value) {
        array_push($auth_ids , $value["id"]);
    }
    $string_query = search_query_parse_inputs($data_request["query"]);
    $request = array("fetch" => " id , username , users_name , email , phone_number , regional_indicator , date_created , account_status"
                    ,"table" => " users "
                    ,"specific" => " id IN(" .  sql_array_query_metacode($auth_ids) . ") AND (" . $string_query . ") AND id > 1"
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

function user_search($data_request , $pdo){
    if(!isset($data_request["query"]))
        return "Unset Search Query";
    $user_group_query_check = 0;
    $server_message = array("server_message" => "Error"
        ,"message" => "No Parameters Were Inserted"
    );
    $page = $data_request["page"];
    $response = user_search_query($data_request , $pdo , $page);
    $server_message["message"] = $response["message"];
    if(isset($response["result"])){
        $server_message["server_message"] = $response["message"];
        $server_message["message"] = $response["result"];
    }
    return $server_message;
}

?>
