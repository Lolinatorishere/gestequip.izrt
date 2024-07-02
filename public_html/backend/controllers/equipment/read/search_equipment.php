<?php

function read_searched_query($data_request , $pdo){
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["limit"])){
        $data_request["limit"] = 8;
    }
    $all_references = equipment_search($data_request , $pdo);
    return $all_references;
}

?>
