<?php

function on_request_grp_load($auth_groups , $data_request , $pdo , $user_id){
    $all_equipment = array();
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => " user_id=" . $_SESSION["id"] . " AND group_id > 1"
                    );
    if($_SESSION["user_type"] === "Admin"){
        $request["specific"] = " group_id > 1 and user_id = 1";
    }
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["current_page"])){
        $request["page"] = $data_request["current_page"];
    }
    $groups = get_queries($request , $pdo);
    $full_groups = array();
    foreach($groups["items"] as $key => $value){
        $request = array("fetch" => " * "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => " id=" . $value["group_id"]
                        );
        array_push($full_groups , get_query($request , $pdo)["items"]);
    }
    $groups["items"] = $full_groups;
    $data_specific = array("groups" => $groups);
    return $data_specific;
}

function get_groups($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => akrrayrray(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    return on_request_grp_load($auth_groups , $data_request , $pdo , $user_id);
}

?>
