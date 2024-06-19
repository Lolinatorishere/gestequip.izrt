<?php

function on_request_all_load($data_request , $pdo){
    $ret = array();
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

function on_request_all_refresh($data_request , $pdo){
    switch($data_request["refresh"]){
        case: 'group':
            $request = array("fetch" => " * "
                            ,"table" => " users_inside_groups "
                            ,"table" => " "
                            )
            return $user_groups;
            break;
    }
}

function read_request_usr($data_request , $pdo){
    if($_SESSION["user_type"] !== "Admin"){
        return array("Error" => "Error", "Auth_Error" => "Error");
    }
    // what queries can data specific have:
    //$data_specific = array("user" => array() ,"group_id" = "");
    if(!isset($data_request["refresh"])){
        return on_request_all_load($data_request , $pdo , $user_id);
    }else{
        return on_request_all_refresh($auth_groups , $data_request , $pdo , $user_id);
    }
    
}

?>
