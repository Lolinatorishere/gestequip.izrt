<?php

function tab_auth_handle($auth_level){
    if($_SESSION["user_type"] === 'Admin')
        return 1;
    return 0;
}

function check_against_auth_groups($groups){
    $auth_groups = $_SESSION["group_auth"]["auth"];
    $checked = array();
    if(is_array($groups)){
        foreach($groups as $group){
            foreach($auth_groups as $auth_group){
                if($auth_group !== $group["group_id"])
                    continue;
                array_push($checked , $group);
            }
        }
    }else{
        foreach($auth_groups as $auth_group){
            if($auth_group !== $groups)
                continue;
            array_push($checked , $groups);
        }
    }
    if(count($checked) === 0){
        array_push($checked , 0);
    }
    return $checked;
}

?>
