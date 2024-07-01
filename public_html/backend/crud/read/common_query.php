<?php

include_once query_generator_dir;

function get_auth_groups($pdo){
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"counted" => 1
                    ,"specific" => " user_id=" . $_SESSION["id"]
                    );
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



function get_queries($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"])){
        $request["limit"] = 20;
    }
    page_check($request);
    $ret = array();
    $request["countingthis"] = 1;
    $sql = common_select_query($request);
//  }else {
//      $sql = common_select_query($request);
//  }
    if($sql == "error")
        return $sql_error;
    //if(!isset($request["total_items"]) && !isset($request["counted"])){
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $total = $statement->fetch();
        $request["total_items"] = $total[0];
        $request["counted"] = 1;
        unset($request["countingthis"]);
        if(!isset($request["page"])){
            $request["page"] = 1;
        }
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql = common_select_query($request);
    //}
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $ret["total_items"] = $request["total_items"];
    $ret["counted"] = 1;
    $ret["page"] = $request["page"];
    $ret["pages"] = $request["pages"];
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetchAll(PDO::FETCH_ASSOC);
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e , true));
    return $sql_error;
}
}

function get_query($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetch(PDO::FETCH_ASSOC);
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e , true));
    return $sql_error;
}
} 

?>
