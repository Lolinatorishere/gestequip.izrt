<?php

function read_all_groups($data_request , $pdo){
    if($_SESSION["user_type"] !== "Admin")
        return "Unauthorised Request";
    $all_equipment = array();
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => " user_id = 1 AND group_id > 1"
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
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
    return $groups;
}

?>

