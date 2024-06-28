<?php
    
function read_your_auth_level($data_request , $pdo){
    printLog($data_request);
    if(!isset($data_request["query"]))
        return "Unset Query";

    if(!isset($data_request["query"]["group_id"])){
        return "Unset Group Id";
    }
    if(intval($data_request["query"]["group_id"]) <= 1){
        return "Invalid Group";
    }
    $group_id = $data_request["query"]["group_id"];
    $reference = array("group_id" => $group_id , "user_id" => $_SESSION["id"]);
    printLog($reference);
    if(validate_reference_existence($reference , $pdo) !== 1){
        return "User Not in Group";
    }
    $all_equipment = array();
    $request = array("fetch" => " user_permission_level "
                    ,"table" => " users_inside_groups "
                    ,"counted" => 1
                    ,"specific" => " user_id=" . $_SESSION["id"] . " AND group_id=" . $group_id
                    );
    return get_query($request , $pdo);
}


?>
