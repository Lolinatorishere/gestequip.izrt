<?php

//Todo migrate this to get request;

function on_request_yur_load($auth_groups , $data_request , $pdo , $user_id){
    $all_equipment = array();
    //$data_request["fetch"] = " * ";
    //$data_request["table"] = " users_inside_groups_equipments ";
    //$data_request["specific"] = "user_id = " . $user_id;
    //$all_equipment = get_equipments($data_request , $pdo);
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types "
                    ,"counted" => 1
                    );
    $table_info = array();
    $equipment_types = get_queries($request , $pdo);
    $data_request["requests"] = array();
    for($i = 0 ; $i < count($equipment_types) ; $i++){
        $table = array("users_inside_groups_equipments" , "equipment" , $equipment_types["items"][$i]["equipment_type"]);
        $request = array("fetch" => " * "
                        ,"table" => $table
                        ,"values" => array("equipment_id" , "id" , "equipment_id" , )
                        ,"specific" => " users_inside_groups_equipments.user_id = " . $user_id
                        );
        array_push($data_request["requests"] , $request);
    }
    $all_equipment = get_equipments($data_request , $pdo);
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types"
                    ,"counted" => 1
                    );
    if(isset($data_request["page"])){
        $request["current_page"] = $data_request["page"];
    }
    if(isset($all_equipment["total_items"])){
        if($all_equipment["total_items"] === 0)
            return;
    }
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
