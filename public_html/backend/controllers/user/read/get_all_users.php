<?php

function read_all_user($data_request , $pdo){
    if(empty($_SESSION["id"]))
        return "Unauthorised Request";
    $ret = array();
    $request = array("fetch" => " id, users_name, username, email, phone_number, regional_indicator "
                    ,"table" => " users "
                    ,"counted" => 1
                    ,"specific" => "id > 1"
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
        $request["limit"] = $data_request["total_items"];
    }
    $all_users = get_queries($request , $pdo);
    if($all_users["total_items"] === 0)
        return array("Error" => "Error", "Server_Error" => "No Users In Database");
    return $all_users;
}

?>

