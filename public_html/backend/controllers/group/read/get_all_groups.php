<?php

function read_all_groups($data_request , $pdo){
    if(!isset($_SESSION["id"]))
        return "Unauthorised Request";
    $all_equipment = array();
    $request = array("fetch" => " * "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => " id > 1"
                        );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
    }
    $groups = get_queries($request , $pdo);
    return $groups;
}

?>

