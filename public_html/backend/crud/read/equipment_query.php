<?php

include_once query_generator_dir;
include_once common_funcs;

function handle_equipment_search($search , &$sql){
    return 0;
    // if($search == null)
    //     return 0
    // switch($search["category"]){
    //     case "user":
    // }
}

// gets all the equipments from certain ids
function get_equipments($request , $pdo){
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"]))
        $request["limit"] = 20;
    $ret = array();
    $equipment_all = array();
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
    $equipment_ids = $statement->fetchAll(PDO::FETCH_ASSOC);
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
        $equipment = $statement->fetch(PDO::FETCH_ASSOC);
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
        $equipment_spec = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$statement)
            return $sql_error;
        $item = array();
        array_push($item, $equipment , $equipment_spec);
        array_push($equipment_all , query_merge_array($item));
    };
    $ret["items"] = $equipment_all;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1; 
    $ret["total_items"] = $request["total_items"];
    return($ret);   
}

function get_equipment($fetch , $equipment_id , $pdo){
    if(!isset($equipment_id))
        return $sql_error;
    $sql_error = array("error" => "error");
    $table = "";
    $sql = "";
    $item = array();
    $ret = array();
    $equipment_selected = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    $request = array("fetch" => ":fetch"
                    ,"table" => "equipment"
                    ,"counted" => 1
                    ,"specific" => "id=:equipment_id" 
                    );
    $sql = common_select_query($request);
    // request is unavailable
    if($sql === "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':equipment_id' , $equipment_id);
    $statement->bindParam(':fetch' , $fetch);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $equipment_default = $statement->fetch(PDO::FETCH_ASSOC);
    if(count($equipment_default) === 0)
        return $sql_error;
    $request = array("fetch" => " * "
                    ,"table" => ":table"
                    ,"counted" => 1
                    ,"specific" => "equipment_id=:equipment_id"
                    );
    $sql = common_select_query($request);
    if($sql === "error")
        return $sql_error;
    // todo create a metafunction that allows the switch case 
    // to search all possible equipments inside the DB
    switch($equipment_default["equipment_type"]){
        case 1:
            $table = "computers";
        break;
        case 2:
            $table .= "phones";
        break;  
        default:
            return $sql_error;
        break;
    }
    $statement = $pdo->prepare($sql);
    $statment->bindParam(':table' , $table);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $equipment_specific = $statement->fetch(PDO::FETCH_ASSOC);
    array_push($item, $equipment_default , $equipment_spec);
    array_push($equipment_selected , query_merge_array($item));
    $ret["success"] = "success";
    $ret["items"] = $equipment_specific;
    return($ret);
}

?>
