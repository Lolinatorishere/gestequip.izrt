<?php

include_once query_generator_dir;
include_once common_funcs;

function get_users_groups($data_request , $pdo){
try{
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => "user_id=" . $data_request["query"]["user_id"] 
                                 . " AND "
                                 . "group_id > 1"
                    );
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"]))
        $request["limit"] = 20;
    $ret = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $sql = common_select_query($request);
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    if(!isset($request["total_items"])){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql = common_select_query($request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    $groups = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach($groups as $array_key => $group){
        $group_request = array("fetch" => " * "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => "id= " . $group["group_id"]
                        );
        $sql = common_select_query($group_request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $user_group = $statement->fetch(PDO::FETCH_ASSOC);
        printLog($user_group);
        foreach($user_group as $key => $value){
            if($key === "id"){
                continue;
            }
            $groups[$array_key][$key] = $value;
        }
    }
    $ret["items"] = $groups;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1;
    $ret["total_items"] = $request["total_items"];
    return($ret);   
}catch(PDOException $e){
    $ret = array("error" => "error"
                ,"PDOException" => $e
                );
}
}

// gets all the equipments from certain ids
function get_groups($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"]))
        $request["limit"] = 20;
    $ret = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $sql = common_select_query($request);
    // request is unavailable
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    if(!isset($request["total_items"])){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql = common_select_query($request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    $groups = $statement->fetchAll(PDO::FETCH_ASSOC);
    $ret["items"] = $groups;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1; 
    $ret["total_items"] = $request["total_items"];
    return($ret);   
}catch(PDOException $e){
    $ret = array("error" => "error"
                ,"PDOException" => $e
                );
}
}

?>
