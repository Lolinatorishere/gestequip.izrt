<?php

function request_handle($tab_valid , $tab , $type_valid){
    // auth guard clause
    // checks if the type_valid is equivilent to data
    if($type_valid === 1){
        if($tab_valid === 0)
            return array(0 => "error" , 1 => "Invalid Tab");
        if(tab_auth_handle($tab_valid) === 0)
            return array(0 => "error" , 1 => "Unautorized Access");
        return array("success" , ui_request($tab));
    }
    if($type_valid === 2){
        require_once pdo_config_dir;
        $user_id = $_SESSION["id"];
        $ret = data_request($tab , $pdo , $user_id);
        unset($pdo);
        if(isset($ret["error"]))
            return array("unavailable" , $ret);
        return array("success" , $ret);
    }
    return array("error");
}

?>
