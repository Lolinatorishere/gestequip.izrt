<?php

function read_request_search($data_request , $pdo){
    // what queries can data specific have:
    if(!isset($_SESSION["group_auth"]))
        return;
    if(!isset($data_request["limit"])){
        $data_request["limit"] = 8;
    }
    return user_search($data_request , $pdo);
}

?>
