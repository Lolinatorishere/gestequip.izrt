<?php

include_once query_generator_dir;

// todo later

function handle_equipment_search($search , &$sql){
    return 0;
    // if($search == null)
    //     return 0
    // switch($search["category"]){
    //     case "user":
    // }
}

function page_check(&$request){
    $pages = 0;
    if(!isset($request["total_pages"]))
        $request["total_pages"] = 1;
    if(!isset($request["page"]))
        $request["page"] = 1;
    if($pages <= 0)
        $pages = 1;
    return;
}

function get_group_equipments($request , $pdo){
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetchAll();
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
} 

// gets all the equipments from certain ids
function get_equipments($request , $pdo){
    $request["limit"] = 20;
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $equipment_all = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $sql = common_select_query($request);
    error_log($sql);
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
    $equipment_ids = $statement->fetchAll();
    foreach($equipment_ids as $eq_ids){
        $sql = "SELECT *
                from equipment
                where id = ?";
        $statement = $pdo->prepare($sql);
        if(!$statement)
            return $sql_error;
        $statement->bindParam(1 , $eq_ids["equipment_id"] , PDO::PARAM_INT);
        $statement->execute();
        if(!$statement)
            return $sql_error;
        $equipment = $statement->fetch();
        if(!$statement)
            return $sql_error;
        $sql = "SELECT *
                FROM ";
        // todo create a metafunction that allows the switch case 
        // to search all possible equipments inside the DB
        switch($equipment["equipment_type"]){
            case 1:
                $sql .= "computers";
            break;
            case 2:
                $sql .= "phones";
            break;  
            default:
                return $sql_error;
            break;
        }
        $sql .= " WHERE equipment_id = ?";
        $statement = $pdo->prepare($sql);
        if(!$statement)
            return $sql_error;
        $statement->bindParam(1 , $equipment["id"] , PDO::PARAM_INT);
        $statement->execute();
        if(!$statement)
            return $sql_error;
        $equipment_spec = $statement->fetch();
        if(!$statement)
            return $sql_error;
        $item = array();
        array_push($item, $equipment , $equipment_spec);
        array_push($equipment_all , merge_array($item));
    };
    $ret["items"] = $equipment_all;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1; 
    $ret["total_items"] = $request["total_items"];
    return($ret);   
}
?>