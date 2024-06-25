<?php

require_once query_generator_dir;

function get_user_group_auth($request , $pdo){
    $sql_error = "";
    $sql = common_select_query($request);
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $groups =  $statement->fetchAll(PDO::FETCH_ASSOC);
    $user_groups = array("auth" => array()
                        ,"own_auth" => array()
                        ,"de_auth" => array()
                        ,"all_groups" => array()
                        ,"total_items" => 0);
    foreach($groups as $group){
        switch($group["user_permission_level"]){
            case 2: // user is a group manager
                array_push($user_groups["auth"] , $group["group_id"])  ;
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            case 1: // user is permited to alter own equipment
                array_push($user_groups["own_auth"] , $group["group_id"]);
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            case 0: // user is only permited to view own equipment
                array_push($user_groups["de_auth"] , $group["group_id"]);
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            default:
                break;
        }
        $user_groups["total_items"]++;
    }
    return $user_groups;
}
?>
