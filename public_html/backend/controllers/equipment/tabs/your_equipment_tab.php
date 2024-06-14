<?php

function on_request_yur_load($auth_groups , $data_request , $pdo , $user_id){
    $data_request["fetch"] = " * ";
    $data_request["table"] = "users_inside_groups_equipments";
    $data_request["specific"] = "user_id = " . $user_id;
    $all_equipment = get_equipments($data_request , $pdo);
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types"
                    ,"counted" => 1
                    );
    if(isset($data_request["page"])){
        $request["current_page"] = $data_request["page"];
    }
    $equipment_types = get_queries($request , $pdo);
    if(count($all_equipment["items"]) == 0)
        return;
    $data_specific = array("equipment" => $all_equipment
                         ,"equipment_types" => $equipment_types 
                         );
    return $data_specific;
}

function read_request_yur($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => akrrayrray(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    return on_request_yur_load($auth_groups , $data_request , $pdo , $user_id);
}

?>
