<?php

function equipment_request_handle($tab_valid , $tab , $type_valid){
    // validity guard clause
    if($tab_valid === 0)
        return array("error");
    // auth guard clause
    if(tab_auth_handle($tab_valid) === 0)
        return array("error");
    // checks if the type_valid is equivilent to data
    if($type_valid === 2){
        require_once pdo_config_dir;
        $user_id = $_SESSION["id"];
        $ret = data_request($tab , $pdo , $user_id);
        unset($pdo);
        if(isset($ret["error"]))
            return array("unavailable" , $ret);
        return array("success" , $ret);
    }
    return array("success" , ui_request($tab));
}

?>
