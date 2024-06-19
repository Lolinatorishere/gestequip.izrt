<?php

function on_request_all_load($data_request , $pdo , $user_id){
    $user_return = " id , username , users_name , email , phone_number , regional_indicator , date_created , account_status";
    $request = array("fetch" => $user_return
                    ,"table" => " users "
                    ,"counted" => 1
                    ,"specific" => "id > 0"
                    ,"sudo_group" => 1
                    );
    if(isset($data_request["page"])){
        $request["current_page"] = $data_request["page"];
    }
    $all_users = get_users($request , $pdo);
    if($all_users["total_items"] === 0)
        return array("Error" => "Error", "Server_Error" => "No Users In Database");
    return $all_users;
}

function read_request_usr($data_request , $pdo , $user_id){
    if($_SESSION["user_type"] !== "Admin"){
        return array("Error" => "Error", "Auth_Error" => "Error");
    }
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => akrrayrray(),"user" => array(),"types_specific" => array());
    return on_request_all_load($data_request , $pdo , $user_id);
}

?>
